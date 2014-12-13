<?php

namespace Salamon\Bundle\CovusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SalamonCovusBundle:Default:index.html.twig', array('name' => $name));
    }
}
