<?php

namespace Tests\Functionalities;

use Illuminate\Contracts\Support\Arrayable;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use Logcomex\PhpUtils\Functionalities\PropertiesAttacherFunctionality;
use Logcomex\PhpUtils\Functionalities\PropertiesExporterFunctionality;
use Tests\TestCase;

/**
 * Class PropertiesAttacherFunctionalityUnitTest
 */
class PropertiesAttacherFunctionalityUnitTest extends TestCase
{
    /**
     * @return void
     * @throws BadImplementationException
     */
    public function test_PassingAllTheProperties_SuccessFlow(): void
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
     * @throws BadImplementationException
     */
    public function test_SomeProperties_SuccessFlow(): void
    {
        $fakeClass = new FakeClassWithPropertiesExporterFunctionality();
        $response = $fakeClass
            ->attachValues([
                'myProtectedProperty' => 'test',
                'myNotFoundProperty' => 'test',
            ]);

        $this->assertNull($response);

        $fakeClassPropertiesValues = $fakeClass->toArray();
        $this->assertEquals(1, $fakeClassPropertiesValues['myPublicProperty']);
        $this->assertEquals('test', $fakeClassPropertiesValues['myProtectedProperty']);
        $this->assertNull($fakeClassPropertiesValues['myPrivateProperty']);
        $this->assertArrayNotHasKey('myNotFoundProperty', $fakeClassPropertiesValues);
    }

    /**
     * @return void
     */
    public function testFailureFlow(): void
    {
        $this->expectException(BadImplementationException::class);
        $this->expectExceptionMessage('You must use the Trait PropertiesExporterFunctionality to use this functionality.');

        $fakeClass = new FakeClassWithoutPropertiesExporterFunctionality();
        $fakeClass->attachValues([]);
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
    public $myPublicProperty = 1;
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
