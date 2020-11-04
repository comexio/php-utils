<?php

use Illuminate\Contracts\Support\Arrayable;
use Logcomex\PhpUtils\Enumerators\ErrorEnum;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use Logcomex\PhpUtils\Functionalities\PropertiesAttacherFunctionality;
use Logcomex\PhpUtils\Functionalities\PropertiesExporterFunctionality;
use Logcomex\PhpUtils\Functionalities\ValuesExporterToArrayFunctionality;

/**
 * Class ValuesExporterToArrayFunctionalityUnitTest
 */
class ValuesExporterToArrayFunctionalityUnitTest extends TestCase
{
    /**
     * @return void
     * @throws BadImplementationException
     */
    public function testToArray_SuccessFlow(): void
    {
        $expectedResponse = [
            'myPublicProperty' => 'test',
            'myProtectedProperty' => 'test',
            'myPrivateProperty' => 'test',
        ];
        $fakeClass = new FakeClassWithArrayableContractAndPropertiesFunctionality();
        $fakeClass->attachValues($expectedResponse);

        $response = $fakeClass->toArray();
        $this->assertIsArray($response);
        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testToArray_WithoutArrayableContract_FailureFlow(): void
    {
        $expectedException = new BadImplementationException(
            ErrorEnum::PHU002,
            'You must implement the Arrayable contract to use this functionality.'
        );
        $this->expectCustomException($expectedException, function () {
            $fakeClass = new FakeClassWithoutArrayableContract();
            $fakeClass->toArray();
        });
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testToArray_WithoutPropertiesFunctionality_FailureFlow(): void
    {
        $expectedException = new BadImplementationException(
            ErrorEnum::PHU001,
            'You must use the Trait PropertiesExporterFunctionality to use this functionality.'
        );
        $this->expectCustomException($expectedException, function () {
            $fakeClass = new FakeClassWithArrayableContractButNotPropertiesFunctionality();
            $fakeClass->toArray();
        });
    }
}

/**
 * Class FakeClassWithArrayableContractAndPropertiesFunctionality
 */
class FakeClassWithArrayableContractAndPropertiesFunctionality implements Arrayable
{
    use PropertiesAttacherFunctionality,
        PropertiesExporterFunctionality,
        ValuesExporterToArrayFunctionality;
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
 * Class FakeClassWithoutArrayableContract
 */
class FakeClassWithoutArrayableContract
{
    use ValuesExporterToArrayFunctionality;
}

/**
 * Class FakeClassWithArrayableContractButNotPropertiesFunctionality
 */
class FakeClassWithArrayableContractButNotPropertiesFunctionality implements Arrayable
{
    use ValuesExporterToArrayFunctionality;
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
