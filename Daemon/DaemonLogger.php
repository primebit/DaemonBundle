<?php
/**
 * Created by PhpStorm.
 * User: prime
 * Date: 17.09.13
 * Time: 15:43
 */

namespace Hq\DaemonsBundle\Daemon;


class DaemonLogger {

    const MESSAGE = 1;
    const IMPORTANT_MESSAGE = 2;
    const PHP_MESSAGE = 3;

    protected $logFolder;
    protected $logName;
    protected $fileHandler;
    protected $logFileModified = false; // флаг изменения лога
    protected $transports = array();
    protected $buffer = '';

    protected $stdoutColors = array(
        'black' => '0;30',
        'dark_gray' => '1;30',
        'blue' => '0;34',
        'light_blue' => '1;34',
        'green' => '0;32',
        'light_green' => '1;32',
        'cyan' => '0;36',
        'light_cyan' => '1;36',
        'red' => '0;31',
        'light_red' => '1;31',
        'purple' => '0;35',
        'light_purple' => '1;35',
        'brown' => '0;33',
        'yellow' => '1;33',
        'light_gray' => '0;37',
        'white' => '1;37'
    );

    /**
     * Инициализация логгера
     *
     * @param string $logFolder путь к папке логов
     * @param string $logName название файла лога
     * @param bool $erase очистить файл лога перед записью
     */
    public function __construct($logFolder, $logName, $erase = false) {
        $this->logFolder = $logFolder;
        $this->logName = $logName;

        if($erase) {
            $this->fileHandler = fopen($this->logFolder . '/' . $this->logName, 'w+');
        } else {
            $this->fileHandler = fopen($this->logFolder . '/' . $this->logName, 'a+');
        }

        return $this->fileHandler ? true : false;
    }

    public function __destruct() {
        /* если что-то выводилось, то добавим еще одну пустую строчку */
        if ($this->logFileModified) {
            if (in_array('file', $this->transports)) {
                fwrite($this->fileHandler, "\n");
                fflush($this->fileHandler);
            }
        }

        if ($this->fileHandler) {
            fclose($this->fileHandler);
        }
    }

    /**
     * Установить требуемые транспорты логирования
     *
     * @param array $outputs массив с названиями транспортов
     */
    public function setTransports(array $outputs) {
        $this->transports = $outputs;
    }

    /**
     * Логировать сообщение
     *
     * @param string $string текст сообщения
     * @param int $type тип сообщения
     */
    public function log($string, $type = self::MESSAGE) {
        $this->logFileModified = true;

        $formatedDate = date('d.m.Y H:i:s');

        $message = '[' . $formatedDate . '] ' . $string . "\n";

        if(in_array('stdout', $this->transports)) {
            $messageCopy = preg_replace_callback("/~([a-z_]+)~/", function($aMatches) use($message){
                return "\033[" . $this->stdoutColors[$aMatches[1]] . "m";
            }, $message);

            echo $messageCopy . "\033[0m";
        }

        if (in_array('file', $this->transports)) {
            $messageCopy = preg_replace_callback("/~([a-z_]+)~/", function($aMatches) use($message){
                return "";
            }, $message);
            fwrite($this->fileHandler, $messageCopy);
            fflush($this->fileHandler);
        }

        // сбрасываем в буфер
        $this->buffer .= $messageCopy . "\n";
    }

    /**
     * Получить содержимое буфера логов
     *
     * @return string
     */
    public function getBuffer() {
        return $this->buffer;
    }

}