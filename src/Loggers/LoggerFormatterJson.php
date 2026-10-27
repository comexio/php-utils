<?php

namespace Logcomex\PhpUtils\Loggers;

use JsonException;
use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

class LoggerFormatterJson implements FormatterInterface
{
    /** @throws JsonException */
    public function format(array|LogRecord $record): string
    {
        if ($record instanceof LogRecord) {
            $record = $record->toArray();
        }

        return json_encode($record, JSON_THROW_ON_ERROR) . "\n";
    }

    /** @throws JsonException */
    public function formatBatch(array $records): string
    {
        $message = '';
        foreach ($records as $record) {
            $message .= $this->format($record);
        }

        return $message;
    }
}
