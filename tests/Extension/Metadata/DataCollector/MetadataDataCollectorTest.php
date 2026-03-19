<?php

namespace WS\Bundle\FormAttributeBundle\Tests\Extension\Metadata\DataCollector;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\DataCollector\FormDataExtractor;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormRegistry;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormType;
use Symfony\Component\Form\ResolvedFormTypeFactory;
use WS\Bundle\FormAttributeBundle\Form\Extension\Metadata\DataCollector\MetadataDataCollector;
use WS\Bundle\FormAttributeBundle\Form\Extension\Metadata\Type\MetadataType;
use WS\Bundle\FormAttributeBundle\Metadata\FormMetadata;

class MetadataDataCollectorTest extends TestCase
{
    #[DataProvider('hasTypeProvider')]
    public function testExtractorDecoration(FormTypeInterface $formType, string $expectedClassname): void
    {
        $form = $this->createBuilder('name')
            ->setType(new ResolvedFormType($formType))
            ->getForm();

        $this->assertSame([
            'id' => 'name',
            'name' => 'name',
            'type_class' => $expectedClassname,
            'synchronized' => true,
            'passed_options' => [],
            'resolved_options' => [],
        ], $this->getDataExtractor()->extractConfiguration($form));
    }

    public static function hasTypeProvider(): \Generator
    {
        yield [
            new MetadataType(new FormMetadata('Foo')),
            'Foo',
        ];

        yield [
            new FormType(),
            FormType::class,
        ];
    }

    private function getDataExtractor(): MetadataDataCollector
    {
        return new MetadataDataCollector(new FormDataExtractor());
    }

    private function createBuilder(string $name, array $options = []): FormBuilder
    {
        return new FormBuilder($name, null, new EventDispatcher(), new FormFactory(new FormRegistry([], new ResolvedFormTypeFactory())), $options);
    }
}
