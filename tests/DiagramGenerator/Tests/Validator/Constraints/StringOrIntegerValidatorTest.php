<?php

namespace DiagramGenerator\Tests\Validator\Constraints;

use DiagramGenerator\Validator\Constraints\StringOrInteger;
use DiagramGenerator\Validator\Constraints\StringOrIntegerValidator;
use PHPUnit\Framework\TestCase;

class StringOrIntegerValidatorTest extends TestCase
{
    /** @var \DiagramGenerator\Validator\Constraints\StringOrInteger $stringOrInteger */
    protected $stringOrInteger;

    /** @var \Symfony\Component\Validator\Context\ExecutionContext $contextMock */
    protected $contextMock;

    /** @var \DiagramGenerator\Validator\Constraints\StringOrIntegerValidator $stringOrIntegerValidator */
    protected $stringOrIntegerValidator;

    public function setUp()
    {
        parent::setUp();

        $this->contextMock = $this->getMock(
            'Symfony\Component\Validator\Context\ExecutionContext', array(), array(), '', false
        );

        $this->stringOrInteger = new StringOrInteger();
        $this->stringOrIntegerValidator = new StringOrIntegerValidator();
        $this->stringOrIntegerValidator->initialize($this->contextMock);
    }

    public function testEmptyValue()
    {
        $this->assertNull($this->stringOrIntegerValidator->validate(null, $this->stringOrInteger));
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testInvalidValue($value)
    {
        $this->contextMock->expects($this->once())
            ->method('addViolation')
            ->with('This value is not valid.', array());

        $this->stringOrIntegerValidator->validate($value, $this->stringOrInteger);
    }

    public function invalidValueProvider()
    {
        return array(
            array(''),
            array('dsji!kdos'),
            array('#kosdkso'),
            array('djsiodjsiodjsiodjsiodjsiojdiojiosjdiosjdois'),
            array('^cxkckxocx'),
            array('1.433'),
            array('1.1'),
        );
    }

    public function testTooLargeInteger()
    {
        $max = 10;
        $this->stringOrInteger->max = $max;

        $this->contextMock->expects($this->once())
            ->method('addViolation')
            ->with('This value should be {{ limit }} or less.', array('{{ limit }}' => $max));

        $this->stringOrIntegerValidator->validate(11, $this->stringOrInteger);
    }

    public function testTooSmallInteger()
    {
        $min = 2;
        $this->stringOrInteger->min = $min;

        $this->contextMock->expects($this->once())
            ->method('addViolation')
            ->with('This value should be {{ limit }} or more.', array('{{ limit }}' => $min));

        $this->stringOrIntegerValidator->validate(1, $this->stringOrInteger);
    }
}
