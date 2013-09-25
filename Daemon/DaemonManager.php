<?php
/**
 * Created by PhpStorm.
 * User: prime
 * Date: 17.09.13
 * Time: 15:47
 */

namespace Hq\DaemonsBundle\Daemon;

use Hq\DaemonsBundle\Entity\DaemonMessage;
use Hq\DaemonsBundle\Entity\DaemonSession;

class DaemonManager {

    const
        DAEMON_SESSION_WORK = 1,
        DAEMON_SESSION_WAIT = 2,
        DAEMON_SESSION_FINISH = 3,
        DAEMON_SESSION_ERROR = 4;

    protected $container;
    protected $application;
    protected $entityManager;
    protected $logger;

    protected $daemon;
    protected $session;
    protected $options = array();
    protected $daemonObj;

    /**
     * Инициализация демона
     *
     * @param \Hq\DaemonsBundle\Daemon\HqDaemonCommand $daemon объект
     * @param $container сервис-контейнер
     * @param $application объект приложения
     */
    public function __construct($daemon, $container, $application) {
        $this->container = $container;
        $this->application = $application;
        $this->entityManager = $this->container->get('doctrine')->getManager();

        $this->daemon = $daemon;
        if($this->getDaemonObj() === false) {
            throw new \Exception('Unknown daemon');
        }

        $this->options = $this->readDaemonOptions();

        $logPath = $application->getKernel()->getRootDir() . "/../logs/";
        $logPath = realpath($logPath);
        $this->logger = new DaemonLogger($logPath, 'daemon_' . $this->daemon->getDaemonId() . '.log');
        $this->logger->setTransports(array('stdout', 'file'));

        if(!$this->getDaemonObj()->getIsActive()) {
            $this->log('~red~Запуск демона запрещен администратором');
            exit(0);
        }

        $this->startSession();
    }

    public function __destruct() {
        $this->shutdown();
    }

    public function shutdown() {
        // Закрываем сессию
        // Если статус соотвестует одному из активных статусов, будет установлен статус нормального завершения
        if($this->getSessionStatus() < self::DAEMON_SESSION_FINISH)
            $this->closeSession();
        else
            $this->closeSession($this->getSessionStatus());

        // Если демон должен отсылать финальные логи, формируем сообщение и отправляем его
        if($this->getDaemonObj()->getSendFinalLog()) {
            $logBuffer = $this->getLogger()->getBuffer();
            $this->sendEmailsToDevelopers('Финальные логи', $logBuffer);
        }
    }

    /**
     * Создание сессии демона в БД
     */
    public function startSession() {
        $this->session = new DaemonSession();
        $this->session->setStartedAt(new \DateTime('now'));
        $this->session->setDaemon($this->getDaemonObj());
        $this->session->setStatus(self::DAEMON_SESSION_WORK);

        $this->entityManager->persist($this->session);
        $this->entityManager->flush();
    }

    /**
     * Изменение статуса сессии в БД
     *
     * @param integer $status статус
     */
    public function updateSession($status) {
        $this->session->setStatus($status);
        $this->entityManager->persist($this->session);
        $this->entityManager->flush();
    }

    /**
     * Завершить сессию с указаным статусом
     *
     * @param int $status статус завершения
     */
    public function closeSession($status = self::DAEMON_SESSION_FINISH) {
        $this->session->setStatus($status);
        $this->session->setFinishedAt(new \DateTIme('now'));
        $this->entityManager->persist($this->session);
        $this->entityManager->flush();
    }

    /**
     * Получить статус текущей сессии
     *
     * @return integer
     */
    public function getSessionStatus() {
        return $this->session->getStatus();
    }

    /**
     * Логировать сообщение и записать сообщение в БД
     *
     * @param string $message текст сообщения
     * @param int $type тип сообщения
     */
    public function log($message, $type = DaemonLogger::MESSAGE) {
        $this->getLogger()->log($message, $type);
        $this->message($message, $type);
    }

    /**
     * записать сообщение парсера в БД
     *
     * @param string $message текст сообщения
     * @param integer $type тип сообщения
     */
    public function message($message, $type) {
        $message = preg_replace('/~([a-z_]+)~/', '', $message);

        $messageObj = new DaemonMessage();
        $messageObj->setCreatedAt(new \DateTime('now'));
        $messageObj->setSession($this->session);
        $messageObj->setText($message);
        $messageObj->setType($type);
        $this->entityManager->persist($messageObj);
        $this->entityManager->flush();

        // отправим разработчикам мыло
        if($type == DaemonLogger::IMPORTANT_MESSAGE && $this->getDaemonObj()->getSendImportantMessages()) {
            $this->sendEmailsToDevelopers('[IMPORTANT]', $message);
        }
    }

    /**
     * Получить значение опции
     *
     * @param string $optionName название опции
     * @return null|mixed
     */
    public function getOption($optionName) {
        if(isset($this->options[$optionName]))
            return $this->options[$optionName];
        return NULL;
    }

    /**
     * Получить объект логгера
     *
     * @return DaemonLogger
     */
    public function getLogger() {
        return $this->logger;
    }

    /**
     * Получить объект демона из БД
     *
     * @return /Hq/DaemonBundle/Entity/Daemon
     */
    public function getDaemonObj($reload = false) {
        if($this->daemonObj == NULL) {
            $this->daemonObj = $this->container
                ->get('doctrine')
                ->getRepository('HqDaemonsBundle:Daemon')
                ->findOneById($this->daemon->getDaemonId());

            if(!$this->daemonObj) {
                $this->getLogger()->log('DaemonManager::getDaemonObj - демон с ID "' . $this->daemon->getDaemonId() . '" не найден в БД', DaemonLogger::IMPORTANT_MESSAGE);
                return false;
            }
        }
        if($reload) {
            $this->entityManager->refresh($this->daemonObj);
        }
        return $this->daemonObj;
    }

    /**
     * Прочитать из БД все опции демона
     *
     * @return array
     */
    protected function readDaemonOptions() {
        $daemonOptions = $this->container
            ->get('doctrine')
            ->getRepository('HqDaemonsBundle:DaemonOption')
            ->findOneByDaemon($this->getDaemonObj());
        if(!$daemonOptions)
            return array();

        $optionsArray = array();
        foreach($daemonOptions as $option) {
            $optionsArray[$option->getName()] = $option->getValue()->getValue();
        }
        return $optionsArray;
    }

    /**
     * Отправить email-сообщение разработчикам
     *
     * @param string $title Заголовок письма
     * @param string $messageText текст письма
     * @return bool
     */
    public function sendEmailsToDevelopers($title, $messageText) {
        if(trim($messageText) == '')
            return false;

        $message = 'Демон: ' . $this->getDaemonObj()->getName() . "\n";
        $message .= 'Запущен: ' . $this->session->getStartedAt()->format('d.m.Y H:i:s') . "\n";
        if($this->getSessionStatus() > self::DAEMON_SESSION_WAIT) {
            $message .= 'Завершен: ' . $this->session->getFinishedAt()->format('d.m.Y H:i:s') . "\n";
            $message .= "Статус завершения: "
                . $this->container->get('translator')->trans('daemon.status.' . $this->getSessionStatus())
                . "\n";
        } else {
            $message .= 'Статус: '
                .$this->container->get('translator')->trans('daemon.status.' . $this->getSessionStatus())
                . "\n";
        }
        $message .= "\n------------------------------------\n\n";
        $message .= $messageText;

        $title = '[DAEMON] ' . $title;

        $developers = $this->container->get('doctrine')
            ->getRepository('HqUserBundle:User')
            ->findBy(array(
                'enabled' => true,
                'expired' => false,
                'is_developer' => true
            ));
        if(!$developers) {
            return false;
        }

        foreach($developers as $developer) {
            $mail = \Swift_Message::newInstance()
                ->setSubject($title)
                ->setFrom('nobody@hq.ctlc.ru')
                ->setTo($developer->getEmail())
                ->setBody($message);
            $this->container->get('mailer')->send($mail);

            echo "Mail to " . $developer->getEmail() . " sent\n";
        }
    }

}