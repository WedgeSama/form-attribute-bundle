<?php

namespace WS\Bundle\FormAttributeBundle\Metadata;

/**
 * Represent the contract of metadata for a FormType.
 *
 * @author Benjamin Georgeault <git@wedgesama.fr>
 */
interface FormMetadataInterface
{
    /**
     * @return class-string
     */
    public function getClassName(): string;

    public function getParent(): string;

    public function getBlockPrefix(): string;

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array;

    /**
     * @return array<string, FieldMetadataInterface>
     */
    public function getFields(): array;
}
