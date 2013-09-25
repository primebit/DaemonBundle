<?php

namespace Hq\DaemonsBundle\Command;

use Hq\DaemonsBundle\Daemon\DaemonLogger;
use Hq\DaemonsBundle\Daemon\SystemWrapper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Hq\DaemonsBundle\Daemon\HqDaemonCommand;

class TestCommand extends HqDaemonCommand {

    protected $daemonId = 1;

    protected function configure() {
        parent::configure();
        $this
            ->setName('hq:test')
            ->setDescription('Тестовая команда');
    }

    public function work(InputInterface $input, OutputInterface $output) {
        $this->daemonManager->getLogger()->log('Привет, я тестовый демон');
        $this->daemonManager->getLogger()->log('~red~Я умею раскрашивать вывод');

        $instances = $this->getRunningInstances();
        $this->daemonManager->getLogger()->log('~blue~Сейчас запущено ' . $instances . " моих экземпляров");
        $this->daemonManager->getLogger()->log('~green~Текущая локаль: ' . $this->translator->getLocale());

        SystemWrapper::printMemoryUsage();

        /** $postRes = $this->queueManager->postToQueue('daemon_1_queue', array('site' => '1', 'extra' => 'blabla'));
        if($postRes) {
            $this->daemonManager->getLogger()->log('~green~Задание добавлено');
        } else {
            $this->daemonManager->getLogger()->log('~red~Ошибка при добавлении задания');
        }
        $queueManager->postToQueue('daemon_test_queue', array('site' => '1', 'extra' => 'blabla'), array(), array(), new \DateTime('+1 day'));
        $queueManager->postToQueue('daemon_test_queue', array('site' => '1', 'extra' => 'blabla'), array(), array(), new \DateTime('+1 day'), new \DateTime('+2 day'));
        */
        $data = $this->consumeFromQueue();
        if($data == false) {
            $this->daemonManager->getLogger()->log('Нет заданий');
        } else {
            var_dump($data);
        }
    }

}