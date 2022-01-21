<?php

namespace DiagramGenerator\Tests\Validator\Constraints;

use DiagramGenerator\Validator\Constraints\CustomCellSize;
use DiagramGenerator\Validator\Constraints\CustomCellSizeValidator;
use PHPUnit\Framework\TestCase;

class CustomCellSizeValidatorTest extends TestCase
{
    /** @var \DiagramGenerator\Validator\Constraints\CustomCellSize $customCellSize */
    protected $customCellSize;

    /** @var \Symfony\Component\Validator\Context\ExecutionContext $contextMock */
    protected $contextMock;

    /** @var \DiagramGenerator\Validator\Constraints\CustomCellSizeValidator $customCellSizeValidator */
    protected $customCellSizeValidator;

    public function setUp()
    {
        parent::setUp();

        $this->contextMock = $this->getMock(
            'Symfony\Component\Validator\Context\ExecutionContext', array(), array(), '', false
        );

        $this->customCellSize = new CustomCellSize();
        $this->customCellSizeValidator = new CustomCellSizeValidator();
        $this->customCellSizeValidator->initialize($this->contextMock);
    }

    public function testEmptyValue()
    {
        $this->assertNull($this->customCellSizeValidator->validate(null, $this->customCellSize));
        $this->assertNull($this->customCellSizeValidator->validate('', $this->customCellSize));
    }

    /**
     * @dataProvider invalidFormatProvider
     */
    public function testInvalidFormat($value)
    {
        $this->contextMock->expects($this->once())
            ->method('addViolation')
            ->with($this->customCellSize->invalidMessage, array('{{ value }}' => $value));

        $this->customCellSizeValidator->validate($value, $this->customCellSize);
    }

    public function invalidFormatProvider()
    {
        return array(
            array('fkdokfoipdsk'),
            array('-1'),
            array('1.2'),
            array('122pxx')
        );
    }

    /**
     * @dataProvider exceedsMaxIndexProvider
     */
    public function testExceedsMaxIndex($value)
    {
        $this->customCellSize->max = 15;

        $this->contextMock->expects($this->once())
            ->method('addViolation')
            ->with(
                $this->customCellSize->maxIndexMessage,
                array('{{ value }}' => $value, '{{ limit }}' => $this->customCellSize->max)
            );

        $this->customCellSizeValidator->validate($value, $this->customCellSize);
    }

    public function exceedsMaxIndexProvider()
    {
        return array(
            array(16),
            array(23),
            array("16"),
            array('20')
        );
    }

    /**
     * @dataProvider lessThanMinIndexProvider
     */
    public function testLessThanMinIndex($value)
    {
        $this->customCellSize->min = 4;

        $this->contextMock->expects($this->once())
            ->method('addViolation')
            ->with(
                $this->customCellSize->minIndexMessage,
                array('{{ value }}' => $value, '{{ limit }}' => $this->customCellSize->min)
            );

        $this->customCellSizeValidator->validate($value, $this->customCellSize);
    }

    public function lessThanMinIndexProvider()
    {
        return array(
            array(3),
            array(0),
            array('2')
        );
    }

    public function testExceedsMaxPx()
    {
        $value = '201px';
        $this->customCellSize->maxPx = 200;

        $this->contextMock->expects($this->once())
            ->method('addViolation')
            ->with(
                $this->customCellSize->maxMessage,
                array('{{ value }}' => $value, '{{ limit }}' => $this->customCellSize->maxPx)
            );

        $this->customCellSizeValidator->validate($value, $this->customCellSize);
    }

    public function testLessThanMinPx()
    {
        $value = '19px';
        $this->customCellSize->minPx = 20;

        $this->contextMock->expects($this->once())
            ->method('addViolation')
            ->with(
                $this->customCellSize->minMessage,
                array('{{ value }}' => $value, '{{ limit }}' => $this->customCellSize->minPx)
            );

        $this->customCellSizeValidator->validate($value, $this->customCellSize);
    }

    /**
     * @dataProvider validValuesProvider
     */
    public function testValidValues($value)
    {
        $this->customCellSize->min = 0;
        $this->customCellSize->max = 3;
        $this->customCellSize->minPx = 20;
        $this->customCellSize->maxPx = 200;

        $this->assertNull($this->customCellSizeValidator->validate($value, $this->customCellSize));
    }

    public function validValuesProvider()
    {
        return array(
            array(3),
            array(15),
            array(199),
            array('30px'),
            array('180px')
        );
    }
}
