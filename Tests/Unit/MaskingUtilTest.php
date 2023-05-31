<?php

declare(strict_types=1);

namespace Modules\HbSupport\Tests\Unit;

use Exception;
use Modules\HbSupport\Utils\MaskingUtil;
use Tests\TestCase;

/**
 * Class MaskingSvcTest
 *
 * @covers \Modules\HbSupport\Utils\MaskingUtil
 */
class MaskingUtilTest extends TestCase
{
    private MaskingUtil $maskingUtil;

    /** {@inheritDoc} */
    public function setUp(): void
    {
        $this->maskingUtil = new MaskingUtil();
        parent::setUp();
    }

    /**
     * @throws Exception
     */
    public function testMaskC2string()
    {
        $result = $this->maskingUtil->maskString('+49170123456789');
        self::assertEquals('+49*********789', $result);
    }

    public function testMaskUrl()
    {
        $result = $this->maskingUtil->maskUrl('https://www.test.de/start?email=max@muster.de');
        self::assertEquals('https:://www.test.de/start', $result);
    }

    /**
     * @throws Exception
     */
    public function testMaskArray()
    {
        $result = $this->maskingUtil->maskArray([
            'test' => 'abcdefghijklmnop',
            'name' => 'Max-Michael Mustermann',
            'emailadress' => 'max-michael@mustermann.de',
            'anschrift' => [
                'city' => 'Musterstadt',
                'zip' => '12345',
                'street' => 'Mustergasse 1',
            ],
            'test2' => null,
        ]);
        self::assertEquals([
            'test' => 'abcdefghijklmnop',
            'name' => 'Max-M************rmann',
            'emailadress' => 'max∗∗∗∗∗∗∗∗@mustermann.de',
            'anschrift' => [
                'city' => 'Musterstadt',
                'zip' => '1***5',
                'street' => 'Mus*******e 1',
            ],
            'test2' => null,
        ], $result);
    }

    public function testMaskArrayWithMaskEverything()
    {
        $result = $this->maskingUtil->maskArray([
            'test' => 'abcdefghijklmnop',
            'name' => 'Max-Michael Mustermann',
            'emailadress' => 'max-michael@mustermann.de',
            'anschrift' => [
                'city' => 'Musterstadt',
                'zip' => '12345',
                'street' => 'Mustergasse 1',
            ],
            'test2' => null,
        ], true);
        self::assertEquals([
            'test' => 'abcd********mnop',
            'name' => 'Max-M************rmann',
            'emailadress' => 'max∗∗∗∗∗∗∗∗@mustermann.de',
            'anschrift' => [
                'city' => 'Mu*******dt',
                'zip' => '1***5',
                'street' => 'Mus*******e 1',
            ],
            'test2' => null,
        ], $result);
    }

    /**
     * @throws Exception
     */
    public function testMaskEmail()
    {
        $result = $this->maskingUtil->maskEmail('max.mustermann@testmail.de');
        self::assertEquals('max∗∗∗∗∗∗∗∗∗∗∗@testmail.de', $result);
    }

    /**
     * @throws Exception
     */
    public function testMaskStdString()
    {
        $result = $this->maskingUtil->maskPhone('+49170123456789', 5, 3);
        self::assertEquals('+4917∗∗∗∗∗∗∗789', $result);
    }
}
