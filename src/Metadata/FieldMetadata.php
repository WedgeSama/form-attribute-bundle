<?php

namespace WS\Bundle\FormAttributeBundle\Metadata;

/**
 * Represent metadata for a FormType's field.
 *
 * @author Benjamin Georgeault <git@wedgesama.fr>
 */
final class FieldMetadata implements FieldMetadataInterface
{
    /**
     * @param class-string|null    $type
     * @param array<string, mixed> $options
     */
    public function __construct(
        private readonly string $name,
        private readonly ?string $type,
        private readonly array $options,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return class-string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
