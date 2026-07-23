<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @return list<string> An array of allowed values for APP_ENV
     *
     * Called by Symfony's KernelTrait::getKernelParameters() via MicroKernelTrait's
     * trait-flattening (Psalm can't trace that vendor-side call). Cannot carry
     * #[\Override] either: PHP only recognizes real class/interface inheritance for
     * that attribute, not this trait-provided method — adding it is a fatal compile
     * error ("no matching parent method exists"); suppressed project-wide for this
     * file in psalm.xml instead, since MissingOverrideAttribute isn't suppressible
     * via inline @psalm-suppress.
     *
     * @psalm-suppress UnusedMethod
     */
    private function getAllowedEnvs(): array
    {
        return ['prod', 'dev', 'test'];
    }
}
