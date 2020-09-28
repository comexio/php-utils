<?php

use Illuminate\Contracts\Support\Jsonable;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use Logcomex\PhpUtils\Functionalities\PropertiesAttacherFunctionality;
use Logcomex\PhpUtils\Functionalities\PropertiesExporterFunctionality;
use Logcomex\PhpUtils\Functionalities\ValuesExporterToJsonFunctionality;

/**
 * Class ValuesExporterToJsonFunctionalityUnitTest
 */
class ValuesExporterToJsonFunctionalityUnitTest extends TestCase
{
    /**
     * @return void
     * @throws BadImplementationException
     */
    public function testToJson_SuccessFlow(): void
    {
        $fakeClass = new FakeClassWithJsonableContractAndPropertiesFunctionality();
        $fakeClass->attachValues([
            'myPublicProperty' => 'test',
            'myProtectedProperty' => 'test',
            'myPrivateProperty' => 'test',
        ]);

        $response = $fakeClass->toJson();
        $this->assertIsString($response);

        $expectedResponse = '{"myPublicProperty":"test","myProtectedProperty":"test","myPrivateProperty":"test"}';
        $this->assertJsonStringEqualsJsonString($expectedResponse, $response);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testToJson_WithoutJsonableContract_FailureFlow(): void
    {
        $expectedException = new BadImplementationException(
            'PHU-006',
            'You must implement the Jsonable contract to use this functionality.'
        );
        $this->expectCustomException($expectedException, function () {
            $fakeClass = new FakeClassWithoutJsonableContract();
            $fakeClass->toJson();
        });
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testToJson_WithoutPropertiesFunctionality_FailureFlow(): void
    {
        $expectedException = new BadImplementationException(
            'PHU-001',
            'You must use the Trait PropertiesExporterFunctionality to use this functionality.'
        );
        $this->expectCustomException($expectedException, function () {
            $fakeClass = new FakeClassWithJsonableContractButNotPropertiesFunctionality();
            $fakeClass->toJson();
        });
    }
}

/**
 * Class FakeClassWithJsonableContractAndPropertiesFunctionality
 */
class FakeClassWithJsonableContractAndPropertiesFunctionality implements Jsonable
{
    use PropertiesAttacherFunctionality,
        PropertiesExporterFunctionality,
        ValuesExporterToJsonFunctionality;
    /**
     * @var
     */
    public $myPublicProperty = 'test';
    /**
     * @var
     */
    protected $myProtectedProperty = 'test';
    /**
     * @var
     */
    private $myPrivateProperty  = 'test';
}

/**
 * Class FakeClassWithoutJsonableContract
 */
class FakeClassWithoutJsonableContract
{
    use ValuesExporterToJsonFunctionality;
}

/**
 * Class FakeClassWithJsonableContractButNotPropertiesFunctionality
 */
class FakeClassWithJsonableContractButNotPropertiesFunctionality implements Jsonable
{
    use ValuesExporterToJsonFunctionality;
    /**
     * @var
     */
    public $myPublicProperty = 'test';
    /**
     * @var
     */
    protected $myProtectedProperty = 'test';
    /**
     * @var
     */
    private $myPrivateProperty  = 'test';
}
