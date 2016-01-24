<?php

namespace Wassa\MPS;
use Psr\Log\LoggerInterface;

/**
 * AbstractPush
 */
abstract class AbstractPush implements PushInterface
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var boolean
     **/
    protected $async;

    /**
     * @var \Closure
     **/
    protected $callback;

    /**
     * @var Logger
     **/
    protected $logger;

    /**
     * @var PushData
     */
    protected $pushData;

    /**
     * @var array
     */
    protected $registrationTokens;

    /**
     * @param PushData $pushData
     * @param $registrationTokens
     * @param $parameters
     * @param LoggerInterface $logger
     * @throws PushException
     */
    public function __construct(PushData $pushData, $registrationTokens, $parameters, LoggerInterface $logger)
    {
        $this->pushData = $pushData;
        $this->registrationTokens = $registrationTokens;

        if (isset($parameters->async)) {
            $this->async = $parameters->async;

            if ($parameters->async) {
                if (isset($parameters->callback)) {
                    $this->callback = $parameters->callback;
                }
                else {
                    throw new PushException("Le paramètre async est vrai mais aucune URL de callback n'est spécifiée");
                }
            }
        }
        else {
            $this->async = false;
        }

        $this->failedMessages = array();
        $this->logger = new Logger($logger);
        $this->parameters = $parameters;
    }

    /**
     * Get async
     *
     * @return boolean
     */
    public function getAsync()
    {
        return $this->async;
    }

    /**
     * Get callback
     *
     * @return string
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param $messages
     * @param $allMessages
     * @param bool|true $messagesArrayIsFailed
     * @return array
     */
    protected function returnResult($messages, $allMessages, $messagesArrayIsFailed = true)
    {
        $diffMessages = array_diff($allMessages, $messages);

        if ($messagesArrayIsFailed) {
            $allOK = count($messages) == 0 ? true : false;
            return array('all_ok' => $allOK, 'success' => $diffMessages, 'error' => $messages);
        }
        else {
            $allOK = count($diffMessages) == 0 ? true : false;
            return array('all_ok' => $allOK, 'success' => $messages, 'error' => $diffMessages);
        }
    }
}
