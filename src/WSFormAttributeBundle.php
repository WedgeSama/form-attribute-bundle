<?php

namespace WS\Bundle\FormAttributeBundle;

use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WS\Bundle\FormAttributeBundle\Attribute\AsFormType;
use WS\Bundle\FormAttributeBundle\DependencyInjection\Compiler\DebugPass;
use WS\Bundle\FormAttributeBundle\DependencyInjection\Compiler\MetadataPass;

final class WSFormAttributeBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new MetadataPass());
        $container->addCompilerPass(new DebugPass());

        $container->registerAttributeForAutoconfiguration(AsFormType::class, static function (ChildDefinition $definition) {
            $definition->addResourceTag('form.metadata.form_type');
        });
    }
}
