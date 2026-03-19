<?php

namespace WS\Bundle\FormAttributeBundle\Form\Extension\Metadata\DataCollector;

use Symfony\Component\Form\Extension\DataCollector\FormDataExtractorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use WS\Bundle\FormAttributeBundle\MetadataFormTypeInterface;

final readonly class MetadataDataCollector implements FormDataExtractorInterface
{
    public function __construct(
        private FormDataExtractorInterface $inner,
    ) {
    }

    public function extractConfiguration(FormInterface $form): array
    {
        $data = $this->inner->extractConfiguration($form);
        $innerType = $form->getConfig()->getType()->getInnerType();

        if ($innerType instanceof MetadataFormTypeInterface) {
            $data['type_class'] = $innerType->getClassName();
        }

        return $data;
    }

    public function extractDefaultData(FormInterface $form): array
    {
        return $this->inner->extractDefaultData($form);
    }

    public function extractSubmittedData(FormInterface $form): array
    {
        return $this->inner->extractSubmittedData($form);
    }

    public function extractViewVariables(FormView $view): array
    {
        return $this->inner->extractViewVariables($view);
    }
}
