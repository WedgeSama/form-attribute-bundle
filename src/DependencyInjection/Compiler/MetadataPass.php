<?php

namespace WS\Bundle\FormAttributeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
use WS\Bundle\FormAttributeBundle\Form\Extension\Metadata\Type\MetadataType;
use WS\Bundle\FormAttributeBundle\Metadata\FormMetadataInterface;

final class MetadataPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->doMetadataExtension($container);
        $this->doRegistry($container);
    }

    private function doMetadataExtension(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('form.metadata.extension')) {
            return;
        }

        $definition = $container->getDefinition('form.metadata.extension');
        $definition->replaceArgument(0, $this->processMetadataTypes($container));
    }

    private function doRegistry(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('form.registry')) {
            return;
        }

        $definition = $container->getDefinition('form.registry');
        $extensions = $definition->getArgument(0);
        $definition->replaceArgument(0, [
            ...$extensions,
            new Reference('form.metadata.extension'),
        ]);
    }

    private function processMetadataTypes(ContainerBuilder $container): Reference
    {
        $metadataMap = [];

        foreach ($container->findTaggedResourceIds('form.metadata.form_type') as $resourceId => $tag) {
            $definition = $container->getDefinition($resourceId);

            if ($definition->isAbstract()) {
                throw new InvalidArgumentException(\sprintf('The resource "%s" tagged "form.metadata.form_type" must not be abstract.', $resourceId));
            }

            $className = $definition->getClass();
            $formTypeId = $resourceId.'.form_type';
            $metadataId = $resourceId.'.metadata';

            $container->setDefinition($metadataId, new Definition(FormMetadataInterface::class))
                ->setFactory([new Reference('form.metadata.default_loader'), 'load'])
                ->addArgument($className)
                ->addTag('form.metadata');

            $container->setDefinition($formTypeId, $d = new Definition(MetadataType::class))
                ->addArgument(new Reference($metadataId))
                ->addTag('form.metadata_type', ['class_name' => $className])
            ;

            $metadataMap[$className] = new Reference($formTypeId);
        }

        return ServiceLocatorTagPass::register($container, $metadataMap);
    }
}
