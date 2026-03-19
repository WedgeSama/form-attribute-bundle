<?php

namespace WS\Bundle\FormAttributeBundle\Tests\Extension\Metadata\Type;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WS\Bundle\FormAttributeBundle\Form\Extension\Metadata\Type\MetadataType;
use WS\Bundle\FormAttributeBundle\Metadata\FieldMetadataInterface;
use WS\Bundle\FormAttributeBundle\Metadata\FormMetadataInterface;

class MetadataTypeTest extends TestCase
{
    public function testGetParent()
    {
        $this->assertEquals('Parent', new MetadataType($this->getMetadata())->getParent());
    }

    public function testConfigureOptions()
    {
        $resolver = new OptionsResolver();
        new MetadataType($this->getMetadata())->configureOptions($resolver);

        $this->assertSame(['label' => 'Foo'], $resolver->resolve());
    }

    public function testBuildForm()
    {
        ($builder = $this->createMock(FormBuilderInterface::class))
            ->expects($this->once())
            ->method('add')
            ->with('foo', null, [
                'label' => 'Foo',
            ]);

        new MetadataType($this->getMetadata())->buildForm($builder, []);
    }

    public function testGetBlockPrefix()
    {
        $this->assertEquals('block_prefix', new MetadataType($this->getMetadata())->getBlockPrefix());
    }

    public function testClassName()
    {
        $this->assertEquals('ClassName', new MetadataType($this->getMetadata())->getClassName());
    }

    private function getMetadata(): FormMetadataInterface
    {
        return new class implements FormMetadataInterface {
            public function getClassName(): string
            {
                return 'ClassName';
            }

            public function getParent(): string
            {
                return 'Parent';
            }

            public function getBlockPrefix(): string
            {
                return 'block_prefix';
            }

            public function getOptions(): array
            {
                return [
                    'label' => 'Foo',
                ];
            }

            public function getFields(): array
            {
                return [
                    'foo' => new class implements FieldMetadataInterface {
                        public function getName(): string
                        {
                            return 'foo';
                        }

                        public function getType(): ?string
                        {
                            return null;
                        }

                        public function getOptions(): array
                        {
                            return [
                                'label' => 'Foo',
                            ];
                        }
                    },
                ];
            }
        };
    }
}
