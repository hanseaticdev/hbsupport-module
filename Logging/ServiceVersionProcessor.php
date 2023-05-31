<?php

namespace Modules\HbSupport\Logging;

use Monolog\LogRecord;

/**
 * Class ServiceVersionProcessor
 */
class ServiceVersionProcessor
{
    /**
     * @codeCoverageIgnore
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        $record->extra['service_version'] = config('app.service_version');

        return $record;
    }
}
