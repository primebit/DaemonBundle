<?php

declare(ticks=1);

namespace Hq\DaemonsBundle\Daemon;

use Hq\DaemonsBundle\Daemon\DaemonSessionManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class HqDaemonCommand extends ContainerAwareCommand {

    /**
     * ID демона в БД
     * При наследовании необходимо переопределить.
     *
     * @var null|integer
     */
    protected $daemonId = NULL;
    protected $halt = false;

    protected $daemonManager;
    protected $translator;

    protected function configure() {
        $this->addOption('dnd', null, InputOption::VALUE_NONE, 'Если установлено, команда не сможет быть демонизирована');
        $this->addOption('kill', null, InputOption::VALUE_NONE, 'Убить все инстансы демона');
    }

    /**
     * Запуск демона, проверки, демонизация
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        // Если установлен флаг --kill, посылаем демонизированному процессу SIGTERM
        if ($input->getOption('kill')) {
            $this->kill();
        }

        $this->translator = $this->getContainer()->get('translator');
        $this->translator->setLocale('ru_RU'); // установим локаль для интернационализации

        $this->initDaemon();
        $this->checkInstances();

        if($this->daemonManager->getDaemonObj()->getIsRealtime()) {
            if ($input->getOption('dnd')) {
                $this->daemonManager->getLogger()->log('~red~Демонизация процесса запрещена');
            } else {
                $this->demonize($input);
            }

            // Инициализация очереди сообщений
            $this->queueManager = $this->getContainer()->get('queue_manager');
            $this->ensureDaemonQueueExists();

            // Execution loop
            while(!$this->halt) {
                try {
                    $this->work($input, $output);
                } catch (\Exception $e) {
                    $this->daemonManager->log(
                        'EXCEPTION: ' . $e->getMessage() . ' in file "' . $e->getFile() . '" on line ' . $e->getLine(),
                        DaemonLogger::PHP_MESSAGE
                    );
                    $this->daemonManager->updateSession(DaemonManager::DAEMON_SESSION_ERROR);
                    $this->daemonManager->shutdown();
                    exit(1);
                }
                $this->getContainer()->get('doctrine')->resetManager();
                $this->daemonManager->getDaemonObj(true);
                if(!$this->halt) {
                    $this->halt = !$this->daemonManager->getDaemonObj()->getIsRealtime();
                    // сбросим entity manager чтобы не жрал память, мудила
                    $this->sleep(10);
                }
            }
        } else {
            $this->work($input, $output);
        }
    }

    /**
     * Тело скрипта
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function work(InputInterface $input, OutputInterface $output) {}

    /**
     * Инициализация менеджера
     */
    protected function initDaemon() {
        if($this->daemonId === NULL) {
            return;
        }
        try {
            $container = $this->getContainer();
            $app = $this->getApplication();
            $this->daemonManager = new DaemonManager($this, $container, $app);
        } catch (\Exception $e) {
            echo "*** " . $e->getMessage() . " ***\n";
            die;
        }
        set_error_handler(array($this, 'errorHandler'), E_ALL);
        register_shutdown_function(array($this, 'fatalErrorShutdownHandler'));
    }

    /**
     * Получить Id текущего демона
     *
     * @return int|null
     */
    public function getDaemonId() {
        return $this->daemonId;
    }

    /**
     * Убить демонизированные процесс
     */
    protected function kill() {
        $symbolicName = $this->getName();
        $symbolicName = preg_replace('/:/', '_', $symbolicName);
        $pidFile = '/tmp/hq_' . $symbolicName . '.pid';

        if(file_exists($pidFile) && is_readable($pidFile)) {
            $pid = file_get_contents($pidFile);
            echo 'Пытаемся прибить процесс с pid=' . $pid . "...\n";
            $killRes = posix_kill($pid, SIGTERM);
            if($killRes)
                echo "\tУспешно\n";
            else
                echo "\tЧто-то пошло не так\n";
            if(is_writable($pidFile)) {
                unlink($pidFile);
            } else {
                echo 'Не удалось удалить pid-файл (' . $pidFile . ")\n";
            }
        } else {
            echo 'Отсутствует pid-файл (' . $pidFile . ")\n";
        }
        exit(0);
    }

    /**
     * Демонизировать процесс
     */
    protected function demonize() {
        $symbolicName = $this->getName();
        $symbolicName = preg_replace('/:/', '_', $symbolicName);
        $pidFile = '/tmp/hq_' . $symbolicName . '.pid';

        $childPid = pcntl_fork();

        // Переподключим соединение с БД, после форка оно падает
        $this->getContainer()->get('database_connection')->close();
        $this->getContainer()->get('database_connection')->connect();
        $this->getContainer()->get('doctrine')->resetManager();

        if($childPid) {
            // выходим из родительского, привязанного к консоли, процесса
            die;
        }

        // делаем основным процессом дочерний
        posix_setsid();
        // засыпаем на некоторое время, достаточное для того, что бы родительский процесс полностью выгрузился
        sleep(5);

        // устанавливаем обработчик сигнала
        if(!pcntl_signal(SIGTERM, array($this, 'sigHandler'))) {
            $this->daemonManager->getLogger()->log('Невозможно установить обработчик сигнала', DaemonLogger::IMPORTANT_MESSAGE);
        }

        if(is_writable(dirname($pidFile))) {
            file_put_contents($pidFile, posix_getpid());
        } else {
            $this->daemonManager->getLogger()->log('Невозможно сохранить ' . $pidFile, DaemonLogger::IMPORTANT_MESSAGE);
        }
    }

    /**
     * Обработчик сигнала SIGTERM
     */
    public function sigHandler() {
        $this->daemonManager->getLogger()->log('Демон получил SIGTERM', DaemonLogger::MESSAGE);
        $this->halt();
    }

    /**
     * Установить флаг остановки демона
     */
    protected function halt() {
        $this->halt = true;
    }

    /**
     * Метод ожидания между итерациями запуска метода work
     * Осуществяет контроль утечек памяти
     *
     * @param $seconds
     */
    protected function sleep($seconds) {
        if (memory_get_usage(true) >= $this->getMemoryLimit()) {
            $this->daemonManager->getLogger()->log(
                'Демон ' . $this->getName() . ' исчерпал выделенную память более чем на 90%. Процесс будет перезапущен.'
            );
            $this->halt();
        }
        sleep($seconds);
    }

    /**
     * Получить текущий лимит памяти
     *
     * @return float|int
     */
    protected function getMemoryLimit() {
        $sysMemoryLimit = ini_get('memory_limit');
        if($sysMemoryLimit < 0) {
            return 1e10;
        }

        $rank = array();
        $factor = 1;
        $memoryLimit = 0;

        if(preg_match('/[^\d]+/i', $sysMemoryLimit, $rank)) {
            switch(strtoupper($rank[0])) {
                case 'K':
                    $factor = 1024;
                    break;
                case 'M':
                    $factor = 1024 * 1024;
                    break;
                case 'G':
                    $factor = 1024 * 1024 * 1024;
                    break;
            }
        }

        if (preg_match('.[\d]+.', $sysMemoryLimit, $rank)) {
            $memoryLimit = $rank[0] * $factor;
        }

        return $memoryLimit - round($memoryLimit * 0.1);
    }

    /**
     * Проверка кол-ва запущенных инстансов демона
     * При привышении установленного лимита текущий инстанс будет остановлен.
     */
    protected function checkInstances() {
        $maxInstances = $this->daemonManager->getDaemonObj()->getMaxInstances();
        if($this->daemonManager->getDaemonObj()->getisRealtime())
            $maxInstances = 1;

        if(
            $maxInstances
            &&  $this->getRunningInstances() > $maxInstances
        ) {
            $this->daemonManager->getLogger()->log('Script "' . $this->daemonManager->getDaemonObj()->getName() . '" is already running');
            exit(0);
        }
    }

    /**
     * Создание очереди сообщений для данного демона
     */
    protected function ensureDaemonQueueExists() {
        $this->queueManager->createQueue('daemon_' . $this->daemonId . '_queue', 4);
    }

    /**
     * Прочитать сообщение из очереди
     *
     * @return string|bool
     */
    protected function consumeFromQueue() {
        try {
            return $this->queueManager->consumeFromQueue('daemon_' . $this->daemonId . '_queue');
        } catch(\Exception $e) {
            return false;
        }
    }

    /**
     * Получить кол-во запущенных инстансов
     *
     * @return int
     */
    protected function getRunningInstances() {
        return SystemWrapper::findActiveProcesses(
            'php app/console ' . $this->getName()
        );
    }

    public function fatalErrorShutdownHandler()
    {
        $lastError = error_get_last();
        if ($lastError['type'] === E_ERROR) {
            $this->errorHandler(E_ERROR, $lastError['message'], $lastError['file'], $lastError['line']);
        }
    }

    /**
     * Обработчик ошибок
     *
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     */
    public function errorHandler($errno, $errstr, $errfile, $errline) {
        $exceptionMessage = '';
        $willDie = false;

        switch ($errno) {
            case E_ERROR:
                $exceptionMessage .= 'Error';
                $willDie = true;
                break;
            case E_WARNING:
                $exceptionMessage .= 'Warning';
                break;
            case E_PARSE:
                $exceptionMessage .= 'Parsing Error';
                break;
            case E_NOTICE:
                $exceptionMessage .= 'Notice';
                break;
            case E_CORE_ERROR:
                $exceptionMessage .= 'Core Error';
                $willDie = true;
                break;
            case E_CORE_WARNING:
                $exceptionMessage .= 'Core Warning';
                break;
            case E_COMPILE_ERROR:
                $exceptionMessage .= 'Compile Error';
                $willDie = true;
                break;
            case E_COMPILE_WARNING:
                $exceptionMessage .= 'Compile Warning';
                break;
            case E_USER_ERROR:
                $exceptionMessage .= 'User Error';
                $willDie = true;
                break;
            case E_USER_WARNING:
                $exceptionMessage .= 'User Warning';
                break;
            case E_USER_NOTICE:
                $exceptionMessage .= 'User Notice';
                break;
            case E_STRICT:
                $exceptionMessage .= 'Strict Standards';
                break;
            case E_RECOVERABLE_ERROR:
                $exceptionMessage .= 'Catchable Fatal Error';
                $willDie = true;
                break;
            default:
                $exceptionMessage .= 'Unkown Error';
        }

        $exceptionMessage .= ': ' . $errstr . ' in file "' . $errfile . '" on line ' . $errline . "\n";
        $this->daemonManager->log($exceptionMessage, DaemonLogger::PHP_MESSAGE);
        $backtrace = debug_backtrace();

        for ($i = count($backtrace) - 1; $i > 0; $i--) {
            $debugRecord = $backtrace[$i];

            if (!empty($debugRecord['file'])) {
                $exceptionMessage .= "\tCalled from ";

                $exceptionMessage .= $debugRecord['file'] . ' [line '. $debugRecord['line'] .'] ';

                $exceptionMessage .= '('. (! empty($debugRecord['class'])
                        ? $debugRecord['class'] . $debugRecord['type']
                        : '');

                $exceptionMessage .= (	! empty($debugRecord['function'])
                    ? $debugRecord['function']
                    : '');
                $exceptionMessage .= ")\n";
            }
        }

        if($this->daemonManager->getDaemonObj()->getSendPhpMessages()) {
            $this->daemonManager->sendEmailsToDevelopers('[PHP_ERROR]', $exceptionMessage);
        }

        if($willDie) {
            $this->daemonManager->updateSession(DaemonManager::DAEMON_SESSION_ERROR);
            $this->daemonManager->shutdown();
            exit(1);
        }

        return true;
    }

}