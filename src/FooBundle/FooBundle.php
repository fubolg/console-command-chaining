<?php

namespace App\FooBundle;

use App\FooBundle\DependencyInjection\FooBundleExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FooBundle extends Bundle
{
    /**
     * @return ExtensionInterface|null
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new FooBundleExtension();
    }
}
