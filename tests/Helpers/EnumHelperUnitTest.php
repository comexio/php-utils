<?php

use Logcomex\PhpUtils\Helpers\EnumHelper;

/**
 * Class EnumHelperUnitTest
 */
class EnumHelperUnitTest extends TestCase
{
    /**
     * @return void
     */
    public function testAll_SuccessFlow(): void
    {
        $response = EnumeratorTestEnum::all();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        $this->assertCount(2, $response);

        $this->assertArrayHasKey('TEST', $response);
        $this->assertArrayHasKey('PASS', $response);

        $this->assertEquals(EnumeratorTestEnum::TEST, $response['TEST']);
        $this->assertEquals(EnumeratorTestEnum::PASS, $response['PASS']);
    }
}

/**
 * Class EnumeratorTestEnum
 */
class EnumeratorTestEnum
{
    use EnumHelper;
    /**
     * @var string
     */
    public const TEST = '1';
    /**
     * @var string
     */
    public const PASS = '2';
}
