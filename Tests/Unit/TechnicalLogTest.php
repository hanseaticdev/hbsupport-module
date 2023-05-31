<?php

declare(strict_types=1);

namespace Modules\HbSupport\Tests\Unit;

use Exception;
use Illuminate\Support\Facades\Log;
use Modules\HbSupport\TechnicalLogs\TechnicalLog;
use Psr\Log\LogLevel;
use Tests\TestCase;

/**
 * Class SouthAPITest
 *
 * @covers \App\Service\SouthLayerResponse
 * @covers \App\Providers\CoreReadProvider
 * @covers \App\Providers\HttpClientProvider
 * @covers \App\Providers\RtFeatureFlagSvcProvider
 * @covers \App\Service\FeatureService
 * @covers \App\Providers\AuditTrailProvider
 * @covers \App\Providers\BVSecureProvider
 * @covers \App\Providers\CustomerDataProvider
 * @covers \App\Providers\InitialPasswordProvider
 * @covers \App\Providers\MobileNumberServiceProvider
 * @covers \App\Providers\ScaTanManagerProvider
 * @covers \App\Providers\SessionManagerProvider
 * @covers \App\Providers\SouthLayerServiceProvider
 * @covers \App\Service\AbstractSouthLayerService
 * @covers \App\Exceptions\HttpResponseException
 * @covers \App\Exceptions\SouthApiNotAvailableException
 */
class TechnicalLogTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_that_log_has_id()
    {
        $log = TechnicalLog::make();
        $this->assertTrue($this->assertIsSha1($log->getId()));
    }

    public function test_simple_log()
    {
        $logSpy = Log::spy();

        TechnicalLog::make()
            ->setLogLevel(LogLevel::NOTICE)
            ->with([
                'foo' => 'bar',
            ])
            ->log('RUNTIME');

        $logSpy->shouldHaveReceived('log')
            ->once()
            ->withArgs(function ($logLevel, $message, $context) {
                $this->assertEquals('notice', $logLevel);
                $this->assertEquals('RUNTIME', $message);
                $this->assertTrue($this->assertIsSha1($context['log_id']));
                unset($context['log_id']);
                $this->assertEquals([
                    'foo' => 'bar',
                ], $context);

                return true;
            });
    }

    public function test_manual_exception_logging()
    {
        $logSpy = Log::spy();

        TechnicalLog::make()
            ->setLogLevel(LogLevel::ERROR)
            ->with([
                'foo' => 'bar',
            ])
            ->logException(new Exception(
                'Error Message',
            ));

        $logSpy->shouldHaveReceived('log')
            ->once()
            ->withArgs(function ($logLevel, $message, $context) {
                $this->assertEquals('error', $logLevel);
                $this->assertEquals('EXCEPTION', $message);
                $this->assertTrue($this->assertIsSha1($context['log_id']));

                return true;
            });
    }
}
