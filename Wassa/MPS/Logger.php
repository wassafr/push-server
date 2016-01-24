<?php

namespace Wassa\MPS;

use Psr\Log\LoggerInterface;

/**
 * Class Logger
 * @package Wassa\MPS
 */
class Logger implements \ApnsPHP_Log_Interface
{
    /**
     * @var LoggerInterface
     */
    private $logReceiver;

    /**
     * @param LoggerInterface $logReceiver
     */
    public function __construct(LoggerInterface $logReceiver)
    {
        $this->logReceiver = $logReceiver;
    }

	/**
	 * Logs a message.
	 *
	 * @param  $sMessage @type string The message.
	 */
	public function log($sMessage)
	{
        $i = strpos($sMessage, ':');
        $level = substr($sMessage, 0, $i);
        $message = substr($sMessage, $i + 1);

        switch ($level) {
            case "INFO":
                $method = 'info';
                break;
            case "STATUS":
                $method = 'notice';
                break;
            case "WARNING":
                $method = 'warning';
                break;
            case "ERROR":
                $method = 'error';
                break;
        }

        $this->logReceiver->$method($message);
	}

    /**
     * @return LoggerInterface
     */
    public function getLogReceiver()
    {
        return $this->logReceiver;
    }
}
