<?php

use Logcomex\PhpUtils\Functionalities\PropertiesExporterFunctionality;

/**
 * Class PropertiesExporterFunctionalityUnitTest
 */
class PropertiesExporterFunctionalityUnitTest extends TestCase
{
    /**
     * @return void
     */
    public function testProperties(): void
    {
        $fakeClass = new FakeClass();
        $response = $fakeClass->properties();

        $this->assertNotNull($response);
        $this->assertIsArray($response);
        $this->assertCount(3, $response);

        $expectedProperties = ['myPublicProperty', 'myProtectedProperty', 'myPrivateProperty'];
        foreach ($expectedProperties as $expectedProperty) {
            $this->assertTrue(
                in_array($expectedProperty, $response),
                "Response haven't the expected property: {$expectedProperty}"
            );
        }
    }
}

/**
 * Class FakeClass
 */
class FakeClass
{
    use PropertiesExporterFunctionality;
    /**
     * @var
     */
    public $myPublicProperty;
    /**
     * @var
     */
    protected $myProtectedProperty;
    /**
     * @var
     */
    private $myPrivateProperty;
}
