<?php

namespace Tests\Loggers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class LoggerFormatterJsonUnitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->deleteLogFile();
    }

    public function testShouldReceiveAndLogAndSaveJsonLog(): void
    {
        /* Arrange */
        $log = Log::channel('json_formatter');

        /* Act */
        $log->info('test message for json', ['relevant_info' => 'relevent info in json']);
        $logFile = File::get(storage_path('logs/json_lumen.log'));
        $logContent = json_decode($logFile, true);

        /* Assert */
        self::assertJson($logFile);
        self::assertSame('test message for json', $logContent['message']);
        self::assertSame('INFO', $logContent['level_name']);
        self::assertSame('relevent info in json', $logContent['context']['relevant_info']);
    }

    public function deleteLogFile(): void
    {
        if (File::exists(storage_path('logs/json_lumen.log'))) {
            File::delete(storage_path('logs/json_lumen.log'));
        }
    }
}
