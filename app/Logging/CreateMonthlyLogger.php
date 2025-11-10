<?php
namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

class CreateMonthlyLogger
{
    public function __invoke($logger)
    {
        $logger = new Logger('cron');

        $file = storage_path('logs/laravel.log');
        $maxFiles = 12; // Keep logs for 12 months

        $handler = new RotatingFileHandler($file, $maxFiles, Logger::DEBUG);
        $handler->setFilenameFormat('{filename}-{date}', 'Y-m');

        $handler->setFormatter(new LineFormatter(null, null, true, true));

        $logger->pushHandler($handler);

        return $logger;
    }
}

