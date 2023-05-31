<?php

namespace Modules\HbSupport\TechnicalLogs;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\HbSupport\Utils\Makeable;
use Modules\HbSupport\Utils\MaskingUtil;
use Modules\SouthAPI\Http\Responses\SouthAPIResponse;
use ReflectionClass;

class TechnicalLog
{
    use Makeable;

    protected string $logLevel;

    protected string $id;

    protected array $context = [];

    protected MaskingUtil $maskingUtil;

    public function __construct()
    {
        $this->maskingUtil = app(MaskingUtil::class);
        $this->generateId();
    }

    public function setLogLevel(string $logLevel): static
    {
        $this->logLevel = $logLevel;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function generateId(): void
    {
        $this->id = sha1(microtime());
        $this->with([
            'log_id' => $this->id,
        ]);
    }

    public function with(array|Arrayable $data, $maskEverything = false): static
    {
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }
        $this->context = array_merge($this->context, $this->maskingUtil->maskArray($data, $maskEverything));

        return $this;
    }

    public function linkResponse(SouthAPIResponse $response): static
    {
        if (! $response->getTechnicalLog()) {
            return $this;
        }

        return $this->with([
            'response_log_id' => $response->getTechnicalLogId(),
        ]);
    }

    public function log(string $message): static
    {
        return $this->sendLog($message);
    }

    public function logException(Exception $exception): static
    {
        return $this->sendLog(
            strtoupper(Str::snake(
                (new ReflectionClass($exception))->getShortName()
            ))
        );
    }

    private function sendLog(string $message): static
    {
        Log::log($this->logLevel, $message, $this->context);

        return $this;
    }
}
