<?php

use Illuminate\Contracts\Support\Arrayable;
use Logcomex\PhpUtils\Enumerators\ErrorEnum;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use Logcomex\PhpUtils\Functionalities\PropertiesAttacherFunctionality;
use Logcomex\PhpUtils\Functionalities\PropertiesExporterFunctionality;
use Logcomex\PhpUtils\Functionalities\ValuesExporterToArrayFunctionality;
use Logcomex\PhpUtils\Functionalities\ValuesExporterToSnakeCaseToArrayFunctionality;

/**
 * Class ValuesExporterToArrayFunctionalityUnitTest
 */
class ValuesExporterToSnakeCaseToArrayFunctionalityUnitTest extends TestCase
{
    /**
     * @return void
     * @throws BadImplementationException
     */
    public function testToSnakeCase_SuccessFlow(): void
    {
        $expectedResponse = [
            'my_public_property' => 'test',
            'my_protected_property' => 'test',
            'my_private_property' => 'test',
        ];
        $fakeClass = new FakeClassWithArrayableContractAndPropertiesFunctionalitySnakeCase();
        $fakeClass->attachValues($expectedResponse);

        $response = $fakeClass->toSnakeCase();
        $this->assertIsArray($response);
        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testToArrayWithoutArrayableContractFailureFlow(): void
    {
        $expectedException = new BadImplementationException(
            ErrorEnum::PHU002,
            'You must implement the Arrayable contract to use this functionality.'
        );
        $this->expectCustomException($expectedException, function () {
            $fakeClass = new FakeClassWithoutArrayableType();
            $fakeClass->toSnakeCase();
        });
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testToArrayWithoutPropertiesFunctionalityFailureFlow(): void
    {
        $expectedException = new BadImplementationException(
            ErrorEnum::PHU001,
            'You must use the Trait PropertiesExporterFunctionality to use this functionality.'
        );
        $this->expectCustomException($expectedException, function () {
            $fakeClass = new FakeClassWithArrayableContractButNotProperties();
            $fakeClass->ToSnakeCase();
        });
    }
}

class FakeClassWithArrayableContractAndPropertiesFunctionalitySnakeCase implements Arrayable
{
    use PropertiesAttacherFunctionality,
        PropertiesExporterFunctionality,
        ValuesExporterToArrayFunctionality,
        ValuesExporterToSnakeCaseToArrayFunctionality;
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
class FakeClassWithoutArrayableType
{
    use ValuesExporterToSnakeCaseToArrayFunctionality;
}

/**
 * Class FakeClassWithArrayableContractButNotPropertiesFunctionality
 */
class FakeClassWithArrayableContractButNotProperties implements Arrayable
{
    use ValuesExporterToArrayFunctionality;
    use ValuesExporterToSnakeCaseToArrayFunctionality;
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
