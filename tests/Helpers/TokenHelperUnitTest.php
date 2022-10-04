<?php

namespace Tests\Helpers;

use Logcomex\PhpUtils\Helpers\TokenHelper;
use Tests\TestCase;

/**
 * Class TokenHelperUnitTest
 */
class TokenHelperUnitTest extends TestCase
{
    /**
     * @return void
     */
    public function testGenerateShortMode(): void
    {
        $response = TokenHelper::generate();

        $this->assertNotNull($response);
        $this->assertIsString($response);
        $this->assertEquals(13, strlen($response));
    }

    /**
     * @return void
     */
    public function testGenerateLongMode(): void
    {
        $response = TokenHelper::generate(false);

        $this->assertNotNull($response);
        $this->assertIsString($response);
        $this->assertEquals(32, strlen($response));
    }
}
