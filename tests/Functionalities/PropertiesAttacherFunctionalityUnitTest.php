<?php

use Illuminate\Contracts\Support\Arrayable;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use Logcomex\PhpUtils\Functionalities\PropertiesAttacherFunctionality;
use Logcomex\PhpUtils\Functionalities\PropertiesExporterFunctionality;
use PHPUnit\Framework\TestCase;

/**
 * Class PropertiesAttacherFunctionalityUnitTest
 */
class PropertiesAttacherFunctionalityUnitTest extends TestCase
{
    /**
     * @return void
     * @throws BadImplementationException
     */
    public function testSuccessFlow(): void
    {
        $fakeClass = new FakeClassWithPropertiesExporterFunctionality();
        $response = $fakeClass->attachValues([
            'myPublicProperty' => 'test',
            'myProtectedProperty' => 'test',
            'myPrivateProperty' => 'test',
        ]);

        $this->assertNull($response);

        $fakeClassPropertiesValues = $fakeClass->toArray();
        $requestedProperties = $fakeClass->properties();
        foreach ($requestedProperties as $requestedProperty) {
            $properyValue = $fakeClassPropertiesValues[$requestedProperty];
            $this->assertEquals(
                'test',
                $properyValue
            );
        }
    }

    /**
     * @return void
     */
    public function testFailureFlow(): void
    {
        $fakeClass = new FakeClassWithoutPropertiesExporterFunctionality();
        try {
            $fakeClass->attachValues([]);

            $this->assertTrue(false);
        } catch (Exception $exception) {
            $this->assertTrue(true);
        }
    }
}

/**
 * Class FakeClassWithoutPropertiesExporterFunctionality
 */
class FakeClassWithoutPropertiesExporterFunctionality
{
    use PropertiesAttacherFunctionality;
}

/**
 * Class FakeClassWithPropertiesExporterFunctionality
 */
class FakeClassWithPropertiesExporterFunctionality implements Arrayable
{
    use PropertiesExporterFunctionality,
        PropertiesAttacherFunctionality;
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

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'myPublicProperty' => $this->myPublicProperty,
            'myProtectedProperty' => $this->myProtectedProperty,
            'myPrivateProperty' => $this->myPrivateProperty,
        ];
    }
}
