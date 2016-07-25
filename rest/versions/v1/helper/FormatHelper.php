<?php

namespace rest\versions\v1\helper;

class FormatHelper
{
    /**
     * Convert date to timestamp
     * @param $date
     * @return int|null
     */
    public static function toTimestamp($date)
    {
        if (!$date) {
            return null;
        }

        $date = \DateTime::createFromFormat('m/d/y', $date);
        $timestamp = $date->getTimestamp();

        return $timestamp;
    }
}