<?php

use Illuminate\Contracts\Support\Arrayable;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use Logcomex\PhpUtils\Functionalities\PropertiesAttacherFunctionality;
use Logcomex\PhpUtils\Functionalities\PropertiesExporterFunctionality;
use Logcomex\PhpUtils\Functionalities\ValuesExporterToArrayFunctionality;
use PHPUnit\Framework\TestCase;

/**
 * Class ValuesExporterToArrayFunctionalityUnitTest
 */
class ValuesExporterToArrayFunctionalityUnitTest extends TestCase
{
    /**
     * @return void
     * @throws BadImplementationException
     */
    public function testSuccessFlow(): void
    {
        $fakeClass = new FakeClassWithArrayableContractAndPropertiesFunctionality();
        $fakeClass->attachValues([
            'myPublicProperty' => 'test',
            'myProtectedProperty' => 'test',
            'myPrivateProperty' => 'test',
        ]);

        $response = $fakeClass->toArray();
        $this->assertIsArray($response);
        $this->assertCount(3, $response);
    }

    /**
     * @return void
     */
    public function testFailureArrayableContractFlow(): void
    {
        $fakeClass = new FakeClassWithoutArrayableContract();
        try {
            $fakeClass->toArray();

            $this->assertTrue(false);
        } catch (Exception $exception) {
            $this->assertInstanceOf(BadImplementationException::class, $exception);
            $this->assertTrue(true);
        }
    }

    /**
     * @return void
     */
    public function testFailurePropertiesFunctionalityFlow(): void
    {
        $fakeClass = new FakeClassWithArrayableContractButNotPropertiesFunctionality();
        try {
            $fakeClass->toArray();

            $this->assertTrue(false);
        } catch (Exception $exception) {
            $this->assertInstanceOf(BadImplementationException::class, $exception);
            $this->assertTrue(true);
        }
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
