<?php

namespace Salamon\Bundle\CovusBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SalamonCovusBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
