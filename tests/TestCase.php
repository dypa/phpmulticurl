<?php
declare(strict_types=1);

use Symfony\Component\Process\Process;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{

    /**
     * @var Process
     */
    private static $webServerProcess;

    /**
     * @var string
     */
    protected static $hostAndPort = "localhost:80";

    public static function setUpBeforeClass(): void
    {
        // https://medium.com/@peter.lafferty/start-phps-built-in-web-server-from-phpunit-9571f38c5045
        self::$webServerProcess = new Process([
            "php",
            "-S",
            self::$hostAndPort,
            "-t",
            realpath(__DIR__)
        ]);
        self::$webServerProcess->start();
    }

    public static function tearDownAfterClass(): void
    {
        self::$webServerProcess->stop();
    }

}