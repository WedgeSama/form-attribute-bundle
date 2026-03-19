<?php

namespace WS\Bundle\FormAttributeBundle\Metadata;

/**
 * Represent the contract of metadata for a FormType's field.
 *
 * @author Benjamin Georgeault <git@wedgesama.fr>
 */
interface FieldMetadataInterface
{
    public function getName(): string;

    /**
     * @return class-string|null
     */
    public function getType(): ?string;

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array;
}
