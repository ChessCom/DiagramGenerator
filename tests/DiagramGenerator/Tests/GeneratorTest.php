<?php

namespace DiagramGenerator\Tests;

use Symfony\Component\Validator\ConstraintViolationList;
use DiagramGenerator\Config;
use DiagramGenerator\Generator;

/**
 * GeneratorTest
 */
class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Symfony\Component\Validator\Validator $validator */
    protected $validatorMock;

    /** @var \DiagramGenerator\Generator $generator */
    protected $generator;

    /** @var \DiagramGenerator\Config $config */
    protected $config;

    /** @var string $rootCacheDir */
    protected $rootCacheDir = '/tmp/diagram_generator_test';

    protected $boardTextureUrl = 'test.png';

    protected $pieceThemeUrl = 'test.png';

    public function setUp()
    {
        parent::setUp();

        $this->validatorMock = $this->getMockBuilder('Symfony\Component\Validator\Validator')
            ->disableOriginalConstructor()
            ->getMock();

        $this->generator = new Generator($this->validatorMock);

        $this->config = new Config();
    }

    /**
     * @expectedException \Exception
     */
    public function testBuildDiagramValidateError()
    {
        $this->assertValidatorMockWithErrors($this->config);

        $this->generator->buildDiagram(
            $this->config, $this->rootCacheDir, $this->boardTextureUrl, $this->pieceThemeUrl
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Size should be 20px or more
     */
    public function testBuildDiagramSmallSize()
    {
        $this->config->setSizeIndex('19px');

        $this->assertValidatorMockWithNoErrors($this->config);

        $this->generator->buildDiagram(
            $this->config, $this->rootCacheDir, $this->boardTextureUrl, $this->pieceThemeUrl
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Size should be 200px or less
     */
    public function testBuildDiagramLargeSize()
    {
        $this->config->setSizeIndex('201px');

        $this->assertValidatorMockWithNoErrors($this->config);

        $this->generator->buildDiagram(
            $this->config, $this->rootCacheDir, $this->boardTextureUrl, $this->pieceThemeUrl
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Piece theme non-existent does not exist
     */
    public function testBuldDiagramNonExistingPieceTheme()
    {
        $this->config->setSizeIndex('200px')
            ->setThemeIndex('non-existent');

        $this->assertValidatorMockWithNoErrors($this->config);

        $this->generator->buildDiagram(
            $this->config, $this->rootCacheDir, $this->boardTextureUrl, $this->pieceThemeUrl
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Board texture non-existent does not exist
     */
    public function testBuildDiagramNonExistingBoardTexture()
    {
        $this->config->setSizeIndex('200px')
            ->setThemeIndex('3d_chesskid')
            ->setTextureIndex('non-existent');

        $this->assertValidatorMockWithNoErrors($this->config);

        $this->generator->buildDiagram(
            $this->config, $this->rootCacheDir, $this->boardTextureUrl, $this->pieceThemeUrl
        );
    }

    private function assertValidatorMockWithErrors(Config $config)
    {
        $constraintViolationMock = $this->getMockBuilder('Symfony\Component\Validator\ConstraintViolation')
            ->disableOriginalConstructor()
            ->getMock();

        $constraintViolationList = new ConstraintViolationList(array($constraintViolationMock));

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($config)
            ->will($this->returnValue($constraintViolationList));
    }

    private function assertValidatorMockWithNoErrors(Config $config)
    {
        $constraintViolationList = new ConstraintViolationList(array());

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($config)
            ->will($this->returnValue($constraintViolationList));
    }
}

