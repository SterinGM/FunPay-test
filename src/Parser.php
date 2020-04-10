<?php
/**
 * Created by PhpStorm.
 * User: Grigoriy Sterin
 * Date: 10.04.2020
 */

namespace App;

use Exception;

class Parser implements ParserInterface
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function parseSms(string $sms): array
    {
        $sms = $this->prepareString($sms);
        $wallet = $this->getWalletNumber($sms);
        $amount = $this->getAmount($sms);
        $password = $this->getPassword($sms);

        return [
            'password' => $password,
            'amount' => $amount,
            'wallet' => $wallet,
        ];
    }

    /**
     * Преобразуем входящие данные, чтобы убрать из текста все теги и переводы на другие строки
     *
     * @param string $sms
     *
     * @return string
     */
    private function prepareString(string $sms): string
    {
        $sms = str_replace(array("\r\n", "\r", "\n"), "<br />", $sms);
        $sms = str_replace('<', ' <', $sms);
        $sms = strip_tags($sms);
        $sms = ' ' . $sms . ' ';

        return $sms;
    }

    /**
     * Проанализировав работу эмулятора, я сделал вывод,
     * что номер крошелька должен всегда начинатья с 41001 и дальше может идти от 8 до 11 цифр
     *
     * @param string $sms
     *
     * @return string
     * @throws Exception
     */
    private function getWalletNumber(string &$sms): string
    {
        $pattern = '/(\D+)(41001\d{8,11})(\D+)/mui';
        preg_match($pattern, $sms, $matches);

        if (isset($matches[2])) {
            $sms = str_replace($matches[2], ' ', $sms);

            return $matches[2];
        } else {
            throw new Exception('Wallet number not found.');
        }
    }

    /**
     * @param string $sms
     *
     * @return float
     * @throws Exception
     */
    private function getAmount(string &$sms): float
    {
        $pattern = '/((\d+)?([.,])(\d+)?)(\ )*?((\w+)?(\.)*?)/mui';
        preg_match($pattern, $sms, $matches);

        if (isset($matches[1])) {
            $sms = str_replace($matches[1], ' ', $sms);
            $amount = str_replace(",", ".", $matches[1]);

            return (float) $amount;
        } else {
            throw new Exception('Amount not found.');
        }
    }

    /**
     * @param string $sms
     *
     * @return string
     * @throws Exception
     */
    private function getPassword(string $sms): string
    {
        $pattern = '/(\d{4,6})/mui';
        preg_match($pattern, $sms, $matches);

        if (isset($matches[1])) {
            return $matches[1];
        } else {
            throw new Exception('Password code not found.');
        }
    }
}