<?php
/*
 * PushData.php
 *
 * Copyright (C) WASSA SAS - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * 22/01/2016
 */

namespace Wassa\MPS;


/**
 * Class PushData
 * @package Wassa\MPS
 */
class PushData
{
    /**
     * @var string
     */
    protected $gcmCollapseKey;
    /**
     * @var array
     */
    protected $gcmPayloadData;
    /**
     * @var int
     */
    protected $apnsBadge;
    /**
     * @var string
     */
    protected $apnsText;
    /**
     * @var string
     */
    protected $apnsCategory;
    /**
     * @var array
     */
    protected $apnsCustomProperties;
    /**
     * @var int
     */
    protected $apnsExpiry;
    /**
     * @var string
     */
    protected $apnsSound;

    /**
     * @return string
     */
    public function getGcmCollapseKey()
    {
        return $this->gcmCollapseKey;
    }

    /**
     * @param string $gcmCollapseKey
     * @return PushData
     */
    public function setGcmCollapseKey($gcmCollapseKey)
    {
        $this->gcmCollapseKey = $gcmCollapseKey;
        return $this;
    }

    /**
     * @return array
     */
    public function getGcmPayloadData()
    {
        return $this->gcmPayloadData;
    }

    /**
     * @param array $gcmPayloadData
     * @return PushData
     */
    public function setGcmPayloadData($gcmPayloadData)
    {
        $this->gcmPayloadData = $gcmPayloadData;
        return $this;
    }

    /**
     * @return int
     */
    public function getApnsBadge()
    {
        return $this->apnsBadge;
    }

    /**
     * @param int $apnsBadge
     * @return PushData
     */
    public function setApnsBadge($apnsBadge)
    {
        $this->apnsBadge = $apnsBadge;
        return $this;
    }

    /**
     * @return string
     */
    public function getApnsText()
    {
        return $this->apnsText;
    }

    /**
     * @param string $apnsText
     * @return PushData
     */
    public function setApnsText($apnsText)
    {
        $this->apnsText = $apnsText;
        return $this;
    }

    /**
     * @return string
     */
    public function getApnsCategory()
    {
        return $this->apnsCategory;
    }

    /**
     * @param string $apnsCategory
     * @return PushData
     */
    public function setApnsCategory($apnsCategory)
    {
        $this->apnsCategory = $apnsCategory;
        return $this;
    }

    /**
     * @return array
     */
    public function getApnsCustomProperties()
    {
        return $this->apnsCustomProperties;
    }

    /**
     * @param array $apnsCustomProperties
     * @return PushData
     */
    public function setApnsCustomProperties($apnsCustomProperties)
    {
        /** @TODO-Bruno: Try sending the JSON without de-encoding (array required by APNS PHP Library) */
        $this->apnsCustomProperties = (array)json_decode($apnsCustomProperties);
        return $this;
    }

    /**
     * @return int
     */
    public function getApnsExpiry()
    {
        return $this->apnsExpiry;
    }

    /**
     * @param int $apnsExpiry
     * @return PushData
     */
    public function setApnsExpiry($apnsExpiry)
    {
        $this->apnsExpiry = $apnsExpiry;
        return $this;
    }

    /**
     * @return string
     */
    public function getApnsSound()
    {
        return $this->apnsSound;
    }

    /**
     * @param string $apnsSound
     * @return PushData
     */
    public function setApnsSound($apnsSound)
    {
        $this->apnsSound = $apnsSound;
        return $this;
    }
}