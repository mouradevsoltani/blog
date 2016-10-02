<?php

namespace REST\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class RESTUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
