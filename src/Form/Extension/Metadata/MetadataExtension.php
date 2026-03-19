<?php

namespace WS\Bundle\FormAttributeBundle\Form\Extension\Metadata;

use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\FormExtensionInterface;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\FormTypeInterface;
use WS\Bundle\FormAttributeBundle\Exception\MetadataException;
use WS\Bundle\FormAttributeBundle\Form\Extension\Metadata\Type\MetadataType;
use WS\Bundle\FormAttributeBundle\Metadata\Loader\LoaderInterface;

/**
 * Responsible for instantiating FormType based on a {@see \WS\Bundle\FormAttributeBundle\Metadata\FormMetadataInterface}.
 *
 * @author Benjamin Georgeault <git@wedgesama.fr>
 */
final class MetadataExtension implements FormExtensionInterface
{
    /**
     * @var array<class-string, FormTypeInterface>
     */
    private array $loadedTypes = [];

    public function __construct(
        private readonly LoaderInterface $loader,
    ) {
    }

    public function getType(string $name): FormTypeInterface
    {
        if (null !== $type = $this->loadedTypes[$name] ?? null) {
            return $type;
        }

        try {
            return $this->loadedTypes[$name] = new MetadataType($this->loader->load($name));
        } catch (MetadataException $e) {
            throw new InvalidArgumentException(\sprintf('Cannot instantiate a "%s" for the given class "%s".', FormTypeInterface::class, $name), previous: $e);
        }
    }

    public function hasType(string $name): bool
    {
        return ($this->loadedTypes[$name] ?? false) || $this->loader->support($name);
    }

    public function getTypeExtensions(string $name): array
    {
        return [];
    }

    public function hasTypeExtensions(string $name): bool
    {
        return false;
    }

    public function getTypeGuesser(): ?FormTypeGuesserInterface
    {
        return null;
    }
}
