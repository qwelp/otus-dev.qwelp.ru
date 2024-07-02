<?php

namespace OtusDev\Log;

use Bitrix\Main\Diag\FileExceptionHandlerLog;
use Bitrix\Main\Diag\ExceptionHandlerFormatter;

class CustomFileExceptionHandlerLog extends FileExceptionHandlerLog
{
    public function write($exception, $logType)
    {
        $text = ExceptionHandlerFormatter::format($exception, false, $this->level);

        $context = [
            'type' => static::logTypeToString($logType),
        ];

        $logLevel = static::logTypeToLevel($logType);

        $message = "{date} - Host: {host} - {type} - {$text}\n";

        $lines = explode("\n", $message);

        foreach ($lines as &$line) {
            $line .= ' OTUS';
        }

        $message = implode("\n", $lines);

        $this->logger->log($logLevel, $message, $context);
    }
}
