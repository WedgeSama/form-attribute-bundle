<?php

namespace WS\Bundle\FormAttributeBundle\Form\Extension\Metadata\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use WS\Bundle\FormAttributeBundle\Metadata\FormMetadataInterface;
use WS\Bundle\FormAttributeBundle\MetadataFormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @internal
 *
 * @author Benjamin Georgeault <git@wedgesama.fr>
 */
final class MetadataType implements MetadataFormTypeInterface
{
    public function __construct(
        private readonly FormMetadataInterface $metadata,
    ) {
    }

    public function getParent(): string
    {
        return $this->metadata->getParent();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults($this->metadata->getOptions());
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($this->metadata->getFields() as $fieldMetadata) {
            $builder->add($fieldMetadata->getName(), $fieldMetadata->getType(), $fieldMetadata->getOptions());
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['form_metadata'] = $this->metadata;
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
    }

    public function getBlockPrefix(): string
    {
        return $this->metadata->getBlockPrefix();
    }

    public function getClassName(): string
    {
        return $this->metadata->getClassName();
    }
}
