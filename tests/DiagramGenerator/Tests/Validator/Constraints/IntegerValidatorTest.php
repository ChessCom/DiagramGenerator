<?php

namespace DiagramGenerator\Tests\Validator\Constraints;

use DiagramGenerator\Validator\Constraints\Integer;
use DiagramGenerator\Validator\Constraints\IntegerValidator;

/**
 * IntegerTest
 */
class IntegerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \DiagramGenerator\Validator\Constraints\Integer $integer */
    protected $integer;

    /** @var \Symfony\Component\Validator\ExecutionContext $contextMock */
    protected $contextMock;

    /** @var \DiagramGenerator\Validator\Constraints\IntegerValidator $integerValidator */
    protected $integerValidator;

    public function setUp()
    {
        $this->contextMock = $this->getMock(
            'Symfony\Component\Validator\ExecutionContext', array(), array(), '', false
        );

        $this->integer = new Integer();
        $this->integerValidator = new IntegerValidator();
        $this->integerValidator->initialize($this->contextMock);
    }

    public function testEmptyValue()
    {
        $this->assertNull($this->integerValidator->validate(null, $this->integer));
        $this->assertNull($this->integerValidator->validate('', $this->integer));
    }

    /**
     * @dataProvider invalidPositiveIntegerProvider
     */
    public function testInvalidPositiveInteger($value)
    {
        $this->integer->positive = true;

        $this->contextMock->expects($this->once())
            ->method('addViolation')
            ->with($this->integer->invalidPositiveMessage, array('{{ value }}' => $value));

        $this->integerValidator->validate($value, $this->integer);
    }

    public function invalidPositiveIntegerProvider()
    {
        return array(
            array(-1),
            array(-15),
            array('fjdiojfdios'),
            array(0),
            array(1.5),
            array(-1.6)
        );
    }

    /**
     * @dataProvider validPositiveIntegerProvider
     */
    public function testValidPositiveInteger($value)
    {
        $this->integer->positive = true;

        $this->assertNull($this->integerValidator->validate($value, $this->integer));
    }

    public function validPositiveIntegerProvider()
    {
        return array(
            array(1),
            array(5),
            array(15)
        );
    }

    /**
     * @dataProvider invalidUnsignedIntegerProvider
     */
    public function testInvalidunsignedInteger($value)
    {
        $this->integer->unsigned = true;

        $this->contextMock->expects($this->once())
            ->method('addViolation')
            ->with($this->integer->invalidUnsignedMessage, array('{{ value }}' => $value));

        $this->integerValidator->validate($value, $this->integer);
    }

    public function invalidUnsignedIntegerProvider()
    {
        return array(
            array(-1),
            array(-15),
            array('fjdiojfdios'),
            array(1.5),
            array(-1.6)
        );
    }

    /**
     * @dataProvider validUnsignedIntegerProvider
     */
    public function testValidUnsignedInteger($value)
    {
        $this->integer->unsigned = true;

        $this->assertNull($this->integerValidator->validate($value, $this->integer));
    }

    public function validUnsignedIntegerProvider()
    {
        return array(
            array(0),
            array(1),
            array(5),
            array(15)
        );
    }

    /**
     * @dataProvider invalidIntegerProvider
     */
    public function testInvalidInteger($value)
    {
        $this->contextMock->expects($this->once())
            ->method('addViolation')
            ->with($this->integer->invalidMessage, array('{{ value }}' => $value));

        $this->integerValidator->validate($value, $this->integer);
    }

    public function invalidIntegerProvider()
    {
        return array(
            array('fdijfds'),
            array('2.43'),
            array('AAAA'),
            array('-1.5454')
        );
    }

    /**
     * @dataProvider validIntegerProvider
     */
    public function testValidInteger($value)
    {
        $this->assertNull($this->integerValidator->validate($value, $this->integer));
    }

    public function validIntegerProvider()
    {
        return array(
            array(1),
            array(0),
            array(3243),
            array(-3),
            array(-43),
            array('-255454'),
            array('434343')
        );
    }
}
