<?php
/**
 * Created by PhpStorm.
 * User: Grigoriy Sterin
 * Date: 10.04.2020
 */

interface ParserInterface
{
    /**
     * @param string $sms
     *
     * @return array
     */
    public function parseSms(string $sms): array;
}