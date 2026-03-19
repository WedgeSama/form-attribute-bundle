<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\Form\Extension\DependencyInjection\DependencyInjectionExtension;
use WS\Bundle\FormAttributeBundle\Metadata\Loader\AttributeLoader;
use WS\Bundle\FormAttributeBundle\Metadata\Loader\LoaderInterface;


return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('form.metadata.attribute_loader', AttributeLoader::class)

        ->alias('form.metadata.default_loader', 'form.metadata.attribute_loader')
        ->alias(LoaderInterface::class, 'form.metadata.default_loader')

        ->set('form.metadata.extension', DependencyInjectionExtension::class)
            ->args([
                abstract_arg('All services with tag "form.metadata_type" are stored in a service locator by FormPass'),
                [],
                [],
            ])
    ;
};
