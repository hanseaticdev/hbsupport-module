<?php

namespace App\Actions;

use Modules\HbSupport\TechnicalLogs\TechnicalLogFactory;

class TrackAndLogRuntimeAction
{
    private float $startTime;

    public function __construct(private readonly TechnicalLogFactory $logger)
    {
    }

    public function startClock()
    {
        $this->startTime = microtime(true);
    }

    public function stopAndLog(string $key)
    {
        $runtime = round(microtime(true) - $this->startTime, 3);
        $this->logger
            ->notice()
            ->with([
                'runtime' => $runtime,
                'key' => $key,
            ])
            ->log('RUNTIME');
    }
}
