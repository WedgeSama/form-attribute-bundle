<?php

namespace WS\Bundle\FormAttributeBundle\Metadata\Loader;

use WS\Bundle\FormAttributeBundle\Attribute\AsFormType;
use WS\Bundle\FormAttributeBundle\Attribute\Type;
use WS\Bundle\FormAttributeBundle\Exception\MetadataException;
use WS\Bundle\FormAttributeBundle\Metadata\FieldMetadata;
use WS\Bundle\FormAttributeBundle\Metadata\FormMetadata;
use WS\Bundle\FormAttributeBundle\Metadata\FormMetadataInterface;

/**
 * Load a {@see FormMetadata} from a class where an attribute {@see AsFormType} is applied.
 *
 * @author Benjamin Georgeault <git@wedgesama.fr>
 *
 * @template T as object
 */
final class AttributeLoader implements LoaderInterface
{
    public function load(string $class): FormMetadataInterface
    {
        $reflectionClass = new \ReflectionClass($class);
        if (null === $asFormType = $this->getOneAttributeInstance(AsFormType::class, $reflectionClass)) {
            throw new MetadataException(\sprintf('The loader "%s" cannot load metadata for class "%s". There is no "%s" attribute on it.', self::class, $class, AsFormType::class));
        }

        $fields = [];
        foreach ($this->getFields($reflectionClass) as $name => $typeAttribute) {
            if (null !== $typeAttribute->getType() && !class_exists($typeAttribute->getType())) {
                throw new MetadataException(\sprintf('The given form type "%s" does not exist for field "%s" of class "%s".', $typeAttribute->getType(), $name, $class));
            }

            $options = $typeAttribute->getOptions();
            if (null === $viewName = $typeAttribute->getName()) {
                $fieldName = $name;
            } else {
                $options['property_path'] = $name;
                $fieldName = $viewName;
            }

            $fields[$fieldName] = new FieldMetadata($fieldName, $typeAttribute->getType(), $options);
        }

        return new FormMetadata(
            $class,
            $this->closestAsFormTypeParent($reflectionClass),
            $fields,
            $asFormType->getOptions(),
        );
    }

    public function support(string $class): bool
    {
        return class_exists($class) && $this->isAsFormType(new \ReflectionClass($class));
    }

    /**
     * @return iterable<string, Type>
     */
    private function getFields(\ReflectionClass $reflectionClass): iterable
    {
        foreach ($reflectionClass->getProperties() as $propRef) {
            if (
                $reflectionClass->getName() !== $propRef->getDeclaringClass()->getName()
                || null === $typeAttribute = $this->getOneAttributeInstance(Type::class, $propRef)
            ) {
                continue;
            }

            yield $propRef->getName() => $typeAttribute;
        }
    }

    /**
     * @param class-string<T> $attributeClass
     *
     * @return T|null
     */
    private function getOneAttributeInstance(string $attributeClass, \ReflectionProperty|\ReflectionClass $ref): ?object
    {
        foreach ($this->getAttributeInstances($attributeClass, $ref) as $attrInstance) {
            return $attrInstance;
        }

        return null;
    }

    /**
     * @param class-string<T> $attributeClass
     *
     * @return iterable<T>
     */
    private function getAttributeInstances(string $attributeClass, \ReflectionProperty|\ReflectionClass $ref): iterable
    {
        /** @var \ReflectionAttribute<T> $attrRef */
        foreach ($ref->getAttributes() as $attrRef) {
            if (is_a($attrRef->getName(), $attributeClass, true)) {
                yield $attrRef->newInstance();
            }
        }
    }

    private function closestAsFormTypeParent(\ReflectionClass $reflectionClass): ?string
    {
        while ($parent = $reflectionClass->getParentClass()) {
            if ($this->isAsFormType($parent)) {
                return $parent->getName();
            }

            $reflectionClass = $parent;
        }

        return null;
    }

    private function isAsFormType(\ReflectionClass $reflectionClass): bool
    {
        return (bool) $reflectionClass->getAttributes(AsFormType::class, \ReflectionAttribute::IS_INSTANCEOF);
    }
}
