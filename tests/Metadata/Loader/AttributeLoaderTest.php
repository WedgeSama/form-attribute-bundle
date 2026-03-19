<?php

namespace WS\Bundle\FormAttributeBundle\Tests\Metadata\Loader;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use WS\Bundle\FormAttributeBundle\Attribute\AsFormType;
use WS\Bundle\FormAttributeBundle\Attribute\Type;
use WS\Bundle\FormAttributeBundle\Exception\MetadataException;
use WS\Bundle\FormAttributeBundle\Metadata\Loader\AttributeLoader;

class AttributeLoaderTest extends TestCase
{
    private AttributeLoader $loader;

    protected function setUp(): void
    {
        $this->loader = new AttributeLoader();
    }

    #[DataProvider('supportProvider')]
    public function testSupport(string $class, bool $expected)
    {
        $this->assertSame($expected, $this->loader->support($class));
    }

    public static function supportProvider(): \Generator
    {
        yield 'Class with AsFormType attribute' => [Model::class, true];
        yield 'Class without AsFormType attribute' => [ModelNoForm::class, false];
    }

    #[DataProvider('loadWithExceptionProvider')]
    public function testLoadWithException(string $class, string $exceptionClass, string $exceptionMessage)
    {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);

        $this->loader->load($class);
    }

    public static function loadWithExceptionProvider(): \Generator
    {
        yield 'Non existing class' => [
            ClassThatDoNotExistAtAll::class,
            \ReflectionException::class,
            'Class "WS\Bundle\FormAttributeBundle\Tests\Metadata\Loader\ClassThatDoNotExistAtAll" does not exist',
        ];

        yield 'Class missing AsFormType attribute' => [
            ModelNoForm::class,
            MetadataException::class,
            'The loader "WS\Bundle\FormAttributeBundle\Metadata\Loader\AttributeLoader" cannot load metadata for class "WS\Bundle\FormAttributeBundle\Tests\Metadata\Loader\ModelNoForm". There is no "WS\Bundle\FormAttributeBundle\Attribute\AsFormType" attribute on it.',
        ];

        yield 'Non existing field type' => [
            ModelErrorField::class,
            MetadataException::class,
            'The given form type "WS\Bundle\FormAttributeBundle\Tests\Metadata\Loader\ClassThatDoNotExistAtAll" does not exist for field "name" of class "WS\Bundle\FormAttributeBundle\Tests\Metadata\Loader\ModelErrorField".',
        ];
    }

    public function testLoad()
    {
        $metadata = $this->loader->load(Model::class);

        $this->assertSame(Model::class, $metadata->getClassName());
        $this->assertSame(FormType::class, $metadata->getParent());
        $this->assertSame([
            'data_class' => Model::class,
        ], $metadata->getOptions());

        $fields = $metadata->getFields();
        $this->assertCount(4, $fields);

        $nameField = $fields['name'];
        $this->assertSame('name', $nameField->getName());
        $this->assertNull($nameField->getType());
        $this->assertEmpty($nameField->getOptions());

        $withOptions = $fields['withOptions'];
        $this->assertSame('withOptions', $withOptions->getName());
        $this->assertNull($withOptions->getType());
        $this->assertSame([
            'label' => 'value',
        ], $withOptions->getOptions());

        $description = $fields['description'];
        $this->assertSame('description', $description->getName());
        $this->assertSame(TextareaType::class, $description->getType());
        $this->assertEmpty($description->getOptions());

        $withTypeAndOptions = $fields['withTypeAndOptions'];
        $this->assertSame('withTypeAndOptions', $withTypeAndOptions->getName());
        $this->assertSame(TextareaType::class, $withTypeAndOptions->getType());
        $this->assertSame([
            'label' => 'value',
        ], $withTypeAndOptions->getOptions());
    }

    #[DataProvider('inheritanceProvider')]
    public function testInheritance(string $class, string $expectedParent, array $directFields)
    {
        $metadata = $this->loader->load($class);

        $this->assertSame($expectedParent, $metadata->getParent());
        $this->assertSame($directFields, array_keys($metadata->getFields()));
    }

    public static function inheritanceProvider(): \Generator
    {
        yield 'No extends' => [Model::class, FormType::class, ['name', 'withOptions', 'description', 'withTypeAndOptions']];
        yield 'With extends' => [ModelChild::class, Model::class, ['anotherField']];
    }

    public function testRenameField()
    {
        $metadata = $this->loader->load(ModelRenameViewField::class);

        $fields = $metadata->getFields();

        $this->assertArrayHasKey('renamedField', $fields);
        $this->assertArrayNotHasKey('originalField', $fields);

        $options = $fields['renamedField']->getOptions();

        $this->assertSame(['property_path' => 'originalField'], $options);
    }
}

#[AsFormType]
class Model
{
    #[Type]
    public string $name;

    #[Type(options: ['label' => 'value'])]
    public string $withOptions;

    #[Type(TextareaType::class)]
    public string $description;

    #[Type(TextareaType::class, ['label' => 'value'])]
    public string $withTypeAndOptions;
}

class ModelNoForm
{
}

#[AsFormType]
class ModelErrorField
{
    #[Type(ClassThatDoNotExistAtAll::class)]
    public string $name;
}

#[AsFormType]
class ModelChild extends Model
{
    #[Type]
    public string $anotherField;
}

#[AsFormType]
class ModelRenameViewField
{
    #[Type(name: 'renamedField')]
    public string $originalField;
}

