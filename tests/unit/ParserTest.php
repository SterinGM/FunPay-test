<?php

use App\Parser;
use App\ParserInterface;
use Codeception\Test\Unit;

class ParserTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /** @var ParserInterface */
    private $parser;

    protected function _before()
    {
        $this->parser = new Parser();
    }

    protected function _after()
    {
    }

    public function testNormalSms()
    {
        $result = $this->parser->parseSms(
            "Пароль: 0720<br />
             Спишется 100,51р.<br />
             Перевод на счет 4100123123123"
        );
        $this->tester->assertEquals($result, [
            'password' => '0720',
            'amount' => '100,51',
            'wallet' => '41001231231232',
        ]);

        $result = $this->parser->parseSms(
            "Перевод на счет 4100123123123<br />
             Спишется 100,51р.<br />
             Пароль: 0720<br />"
        );
        $this->tester->assertEquals($result, [
            'password' => '0720',
            'amount' => '100,51',
            'wallet' => '41001231231232',
        ]);

        $result = $this->parser->parseSms(
            "Пароль: 0720.<br />
             Спишется 100,51р.<br />
             Перевод на счет 4100123123123."
        );
        $this->tester->assertEquals($result, [
            'password' => '0720',
            'amount' => '100,51',
            'wallet' => '41001231231232',
        ]);

        $result = $this->parser->parseSms(
            "Пароль: 10720.<br />
             Спишется 100.51 руб.<br />
             Перевод на счет 4100123123123123."
        );
        $this->tester->assertEquals($result, [
            'password' => '0720',
            'amount' => '100,51',
            'wallet' => '4100123123123123',
        ]);

        $result = $this->parser->parseSms(
            "Пароль: 10720\n
             Спишется 100.51руб\n
             Перевод на счет 4100123123123123\n"
        );
        $this->tester->assertEquals($result, [
            'password' => '0720',
            'amount' => '100,51',
            'wallet' => '4100123123123123',
        ]);

        $result = $this->parser->parseSms(
            "Пароль: 10720 Спишется 100.51руб Перевод на счет 4100123123123123"
        );
        $this->tester->assertEquals($result, [
            'password' => '0720',
            'amount' => '100,51',
            'wallet' => '4100123123123123',
        ]);

        $result = $this->parser->parseSms(
            "   Пароль: 10720   Спишется 100.51руб   Перевод на счет 4100123123123123   "
        );
        $this->tester->assertEquals($result, [
            'password' => '0720',
            'amount' => '100,51',
            'wallet' => '4100123123123123',
        ]);
    }

    public function testFailedSms()
    {
        $this->tester->expectThrowable(Exception::class, $this->parser->parseSms(
            "Пароль: 10720\n
             Спишется 100.51руб\n
             Перевод на счет 4100123123123123123123\n"
        ));

        $this->tester->expectThrowable(Exception::class, $this->parser->parseSms(
            "Пароль: 10720\n
             Спишется 100.51руб\n
             Перевод на счет 4100123123\n"
        ));

        $this->tester->expectThrowable(Exception::class, $this->parser->parseSms(
            "Пароль: 10720\n
             Спишется 100.51руб\n
             Перевод на счет 123123123123123\n"
        ));

        $this->tester->expectThrowable(Exception::class, $this->parser->parseSms(
            "Пароль: 10720\n
             Спишется 100.51руб\n"
        ));

        $this->tester->expectThrowable(Exception::class, $this->parser->parseSms(
            "Пароль: 10720\n
             Перевод на счет 4100123123123123\n"
        ));

        $this->tester->expectThrowable(Exception::class, $this->parser->parseSms(
            "Спишется 100.51руб\n
             Перевод на счет 4100123123123123\n"
        ));
    }
}