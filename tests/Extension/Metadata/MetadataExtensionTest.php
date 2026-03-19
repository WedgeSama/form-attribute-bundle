<?php

namespace WS\Bundle\FormAttributeBundle\Tests\Extension\Metadata;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use WS\Bundle\FormAttributeBundle\Exception\MetadataException;
use WS\Bundle\FormAttributeBundle\Form\Extension\Metadata\MetadataExtension;
use WS\Bundle\FormAttributeBundle\Form\Extension\Metadata\Type\MetadataType;
use WS\Bundle\FormAttributeBundle\Metadata\FormMetadataInterface;
use WS\Bundle\FormAttributeBundle\Metadata\Loader\LoaderInterface;

class MetadataExtensionTest extends TestCase
{
    #[DataProvider('hasTypeProvider')]
    public function testHasType(string $class, bool $expected)
    {
        ($loader = $this->createMock(LoaderInterface::class))
            ->expects($this->once())
            ->method('support')
            ->with($class)
            ->willReturn($expected);

        $this->assertSame($expected, $this->getExtension($loader)->hasType($class));
    }

    public static function hasTypeProvider(): \Generator
    {
        yield [Model::class, true];
        yield [Model::class, false];
    }

    public function testGetTypeWithException()
    {
        ($loader = $this->createMock(LoaderInterface::class))
            ->expects($this->once())
            ->method('load')
            ->willThrowException(new MetadataException());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot instantiate a "Symfony\Component\Form\FormTypeInterface" for the given class "Foo".');
        $this->getExtension($loader)->getType('Foo');
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testGetType()
    {
        ($loader = $this->createMock(LoaderInterface::class))
            ->expects($this->once())
            ->method('load')
            ->with('Foo')
            ->willReturn($this->createMock(FormMetadataInterface::class))
        ;

        $extension = $this->getExtension($loader);

        $this->assertInstanceOf(MetadataType::class, $extension->getType('Foo'));
    }

    private function getExtension(LoaderInterface $loader): MetadataExtension
    {
        return new MetadataExtension($loader);
    }
}

