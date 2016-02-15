<?php

namespace Wassa\MPS;

use Psr\Log\LoggerInterface;

/**
 * Class MultiPushServer
 * @package Wassa\MPS
 */
class MultiPushServer
{
    const SEND_APNS  = 1;
    const SEND_GCM   = 2;
    const SEND_BOTH  = 3;

    /**
     * @var array
     */
    private $_config = ['apns', 'gcm'];

    /**
     * @var int
     */
    private $_mode;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param string $api_key
     * @param boolean $dryRun
     * @param string $environment
     * @param string $prod_cert
     * @param string $sand_cert
     * @param string $ca_cert
     * @param LoggerInterface $logger
     */
    public function __construct($api_key,
                                $dryRun,
                                $environment,
                                $prod_cert,
                                $sand_cert,
                                $ca_cert,
                                LoggerInterface $logger)
    {
        $this->_config['gcm']['api_key'] = $api_key;
        $this->_config['gcm']['dry_run'] = $dryRun;
        $this->_config['apns']['environment'] = $environment;
        $this->_config['apns']['prod_cert'] = $prod_cert;
        $this->_config['apns']['sand_cert'] = $sand_cert;
        $this->_config['apns']['ca_cert'] = $ca_cert;
        $this->logger = $logger;
    }

    /**
     * @return int
     */
    public function getMode()
    {
        return $this->_mode;
    }

    /**
     * @param int $mode
     *
     * @return MultiPushServer
     */
    public function setMode($mode)
    {
        $this->_mode = $mode;

        return $this;
    }

    /**
     * @param PushData $pushData
     * @param array $registrationTokens
     * @param array $badges
     * @return array
     */
    public function send(PushData $pushData, $registrationTokens, $badges = null)
    {
        if (($this->_mode & self::SEND_APNS) == self::SEND_APNS) {
            $pushInfo['apns']['environment'] = $this->_config['apns']['environment'];
            $pushInfo['apns']['prod_cert'] = $this->_config['apns']['prod_cert'];
            $pushInfo['apns']['sand_cert'] = $this->_config['apns']['sand_cert'];
            $pushInfo['apns']['ca_cert'] = $this->_config['apns']['ca_cert'];
            $iosPush = new IosPush($pushData, $registrationTokens, $badges, $pushInfo['apns'], $this->logger);

            return $iosPush->sendPush();
        }

        if (($this->_mode & self::SEND_GCM) == self::SEND_GCM) {
            $pushInfo['gcm']['api_key'] = $this->_config['gcm']['api_key'];
            $pushInfo['gcm']['dry_run'] = $this->_config['gcm']['dry_run'];
            $androidPush = new AndroidPush($pushData, $registrationTokens, $pushInfo['gcm'], $this->logger);

            return $androidPush->sendPush();
        }
    }
} 