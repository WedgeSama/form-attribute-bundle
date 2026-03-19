<?php

namespace WS\Bundle\FormAttributeBundle;

use Symfony\Component\Form\FormTypeInterface;

/**
 * @author Benjamin Georgeault <git@wedgesama.fr>
 */
interface MetadataFormTypeInterface extends FormTypeInterface
{
    /**
     * Returns the FQCN of the class representing the FormType.
     *
     * @return class-string
     */
    public function getClassName(): string;
}
