<?php

namespace WS\Bundle\FormAttributeBundle\Tests\Metadata;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use WS\Bundle\FormAttributeBundle\Metadata\FormMetadata;

class FormMetadataTest extends TestCase
{
    public function testDefaultParent()
    {
        $this->assertSame(FormType::class, new FormMetadata('Foo')->getParent());
    }

    public function testDataClass()
    {
        $metadata = new FormMetadata('Foo');
        $this->assertSame('Foo', $metadata->getOptions()['data_class']);

        $metadata = new FormMetadata('Foo', null, [], [
            'data_class' => 'Bar',
        ]);
        $this->assertSame('Foo', $metadata->getOptions()['data_class']);
    }
}

