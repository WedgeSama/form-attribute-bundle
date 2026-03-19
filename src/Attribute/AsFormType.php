<?php

namespace WS\Bundle\FormAttributeBundle\Attribute;

/**
 * Register a model class (e.g. DTO, entity, model, etc...) as a FormType.
 *
 * @author Benjamin Georgeault <git@wedgesama.fr>
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AsFormType
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        private readonly array $options = [],
    ) {
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
