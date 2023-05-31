<?php

namespace Modules\HbSupport\TechnicalLogs;

use Psr\Log\LogLevel;

class TechnicalLogFactory
{
    public function error(): TechnicalLog
    {
        return TechnicalLog::make()->setLogLevel(LogLevel::ERROR);
    }

    public function notice(): TechnicalLog
    {
        return TechnicalLog::make()->setLogLevel(LogLevel::NOTICE);
    }

    public function info(): TechnicalLog
    {
        return TechnicalLog::make()->setLogLevel(LogLevel::INFO);
    }

    public function debug(): TechnicalLog
    {
        return TechnicalLog::make()->setLogLevel(LogLevel::DEBUG);
    }
}
