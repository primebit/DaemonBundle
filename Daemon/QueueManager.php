<?php
namespace Hq\DaemonsBundle\Daemon;

use Doctrine\ORM\Query\ResultSetMapping;
use Hq\DaemonsBundle\Utils\PgUtils;

class QueueManager {

    protected $doctrine;
    protected $entityManager;

    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
        $this->entityManager = $doctrine->getManager();
    }

    /**
     * Создать очередь
     *
     * @param string $queueName название очереди
     * @param int $connections кол-во подключений
     * @return bool
     */
    public function createQueue($queueName, $connections = 2) {
        $rsm = new ResultSetMapping();
        try {
            $query = $this->entityManager->createNativeQuery(
                "SELECT mbus.create_queue('" . $queueName . "', '" . $connections . "')",
                $rsm
            );
            $result = $query->getResult();
        } catch(\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Добавить сообщений в очередь
     *
     * @param string $queueName название очереди
     * @param array $data массив данных для записи в сообщение
     * @param array $headers заголовки сообщений
     * @param array $properties свойства сообщений
     * @param \DateTime|null $dalayUntil отложить доставку до
     * @param \DateTime|null $expiresAt срок действия сообщений
     * @return bool
     */
    public function postToQueue($queueName, array $data, array $headers=array(), array $properties = array(), $dalayUntil = null, $expiresAt = null) {
        $rsm = new ResultSetMapping();
        $queryStr = "SELECT mbus.post('"
            . $queueName . "', '" . $this->toPg($data) . "', '" . $this->toPg($headers)
            . "', '" . $this->toPg($properties) . "', " . $this->dateToPg($dalayUntil) . ", " . $this->dateToPg($expiresAt) . ")";
        try {
            $query = $this->entityManager->createNativeQuery(
                $queryStr,
                $rsm
            );
            $result = $query->getResult();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Получить сообщений из очереди
     *
     * @param string $queueName название очереди
     * @return mixed
     * @throws \Exception
     */
    public function consumeFromQueue($queueName) {
        $queryStr = "SELECT mbus.consume_queue('". $queueName . "')";
        $query = $this->entityManager->getConnection()->prepare($queryStr);
        $query->execute();
        $data = $query->fetchAll();

        if(count($data) != 1) {
            throw new \Exception('Some troubles while consume queue');
        } else {
            $result = json_decode($data[0]['consume_queue'], true);
            return $result;
        }
    }

    /**
     * Преобразовать массив в hstore
     *
     * @param array $data массик данных
     * @return string
     */
    public function toPg($data)
    {
        $hstore = PgUtils::hstoreFromPhp($data);
        return $hstore;
    }

    /**
     * Преобразовать DateTime в строку формата ISO-8601
     *
     * @param $date
     * @return string
     */
    public function dateToPg($date) {
        if($date === null)
            return 'NULL';
        else
            return "'" . $date->format('Y-m-d H:i:s') . "'";
    }

}