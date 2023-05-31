<?php

namespace Modules\HbSupport\Logging;

use Monolog\Level;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;

/**
 * Class MonologTap
 *
 * @codeCoverageIgnore
 */
class MonologTap
{
    public function __invoke($logger): void
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor(new WebProcessor(null, ['url', 'http_method']));
            $handler->pushProcessor(new ServiceVersionProcessor());
            $handler->pushProcessor(new IntrospectionProcessor(Level::Debug, [
                'TechnicalLogs\\',
                'TrackAndLogRuntimeAction',
                'Illuminate\\Support\\Facades\\Facade',
                'Illuminate\\Log\\',
            ], 0));
        }
    }
}
