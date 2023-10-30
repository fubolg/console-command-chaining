<?php

namespace App\BarBundle;

use App\BarBundle\DependencyInjection\BarBundleExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BarBundle extends Bundle
{
    /**
     * @return ExtensionInterface|null
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new BarBundleExtension();
    }
}
