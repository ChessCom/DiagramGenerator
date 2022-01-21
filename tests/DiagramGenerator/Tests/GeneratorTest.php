<?php

namespace DiagramGenerator\Tests;

use Symfony\Component\Validator\ConstraintViolationList;
use DiagramGenerator\Config;
use DiagramGenerator\Config\Texture;
use DiagramGenerator\Generator;
use PHPUnit\Framework\TestCase;

/**
 * GeneratorTest
 */
class GeneratorTest extends TestCase
{
    /** @var \Symfony\Component\Validator\Validator\ValidatorInterface $validator */
    protected $validatorMock;

    /** @var \DiagramGenerator\Generator $generator */
    protected $generator;

    /** @var \DiagramGenerator\Config $config */
    protected $config;

    /** @var string $rootCacheDir */
    protected $rootCacheDir = '/tmp/diagram_generator_test';

    protected $boardTextureUrl = 'test.png';

    protected $pieceThemeUrl = 'test.png';

    protected function setUp(): void
    {
        parent::setUp();

        $this->validatorMock = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->generator = new Generator($this->validatorMock);

        $this->config = new Config();
    }

    public function testBuildBoardValidateError()
    {
        $this->expectException('Exception');

        $this->assertValidatorMockWithErrors($this->config);

        $this->generator->buildBoard(
            $this->config, $this->rootCacheDir, $this->boardTextureUrl, $this->pieceThemeUrl
        );
    }

    public function testBuildBoardSmallSize()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Size should be 20px or more');

        $this->config->setSizeIndex('19px');

        $this->assertValidatorMockWithNoErrors($this->config);

        $this->generator->buildBoard(
            $this->config, $this->rootCacheDir, $this->boardTextureUrl, $this->pieceThemeUrl
        );
    }

    public function testBuildBoardLargeSize()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Size should be 200px or less');

        $this->config->setSizeIndex('201px');

        $this->assertValidatorMockWithNoErrors($this->config);

        $this->generator->buildBoard(
            $this->config, $this->rootCacheDir, $this->boardTextureUrl, $this->pieceThemeUrl
        );
    }

    public function testBuldBoardNonExistingPieceTheme()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Piece theme non-existent does not exist');

        $this->config->setSizeIndex('200px')
            ->setPieceIndex('non-existent');

        $this->assertValidatorMockWithNoErrors($this->config);

        $this->generator->buildBoard(
            $this->config, $this->rootCacheDir, $this->boardTextureUrl, $this->pieceThemeUrl
        );
    }

    public function testBuildBoardNonExistingBoardTexture()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Board texture non-existent does not exist');

        $this->config->setSizeIndex('200px')
            ->setPieceIndex('3d_chesskid')
            ->setTexture(new Texture('non-existent', 'non-existent', 'png'));

        $this->assertValidatorMockWithNoErrors($this->config);

        $this->generator->setPieceThemes(array('3d_chesskid'));

        $this->generator->buildBoard(
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
