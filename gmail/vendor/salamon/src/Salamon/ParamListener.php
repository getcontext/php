<?php
/**
 * Created by PhpStorm.
 * User: landlord
 * Date: 8/31/14
 * Time: 2:47 PM
 */

namespace Salamon;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ParamListener
{
    const PARAM_NAME = 'parameter';

    public function onKernelRequest(GetResponseEvent $event)
    {

        if ($event->getRequestType() == HttpKernelInterface::MASTER_REQUEST) {
            if ($event->getRequest()->get(self::PARAM_NAME) != null) {
                echo $event->getRequest()->get(self::PARAM_NAME);
            }
        }
    }
} 