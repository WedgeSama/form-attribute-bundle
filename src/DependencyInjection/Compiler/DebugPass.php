<?php

namespace WS\Bundle\FormAttributeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use WS\Bundle\FormAttributeBundle\Form\Extension\Metadata\DataCollector\MetadataDataCollector;

final class DebugPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('data_collector.form.extractor')) {
            return;
        }

        $definition = new Definition(MetadataDataCollector::class)
            ->setDecoratedService('data_collector.form.extractor')
            ->setArguments([new Reference('.inner')])
        ;

        $container->setDefinition('form.metadata.data_collector.form.extractor', $definition);
    }
}
