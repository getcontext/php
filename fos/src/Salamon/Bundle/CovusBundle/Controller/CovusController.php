<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Salamon\Bundle\CovusBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Salamon\Bundle\CovusBundle\Entity\Foo;
use Salamon\Bundle\CovusBundle\Entity\User;
use Salamon\Bundle\CovusBundle\Form\AllTrueFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CovusController extends Controller
{
    public function setAllTrueAction(Request $request)
    {
        /**@var $user \Salamon\Bundle\CovusBundle\Entity\User */
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $foo = $this->getDoctrine()
            ->getRepository('SalamonCovusBundle:Foo')
            ->find($user->getId());

        if (!$foo) {
            $foo = new Foo();
            $foo->setVal1(false);
            $foo->setVal2(false);
            $foo->setVal3(false);
            $foo->setVal4(false);
            $foo->setVal5(false);
        }


        $statusCode = Response::HTTP_OK;
        if ($foo->getVal1() &&
            $foo->getVal2() &&
            $foo->getVal3() &&
            $foo->getVal4() &&
            $foo->getVal5()
        ) {
            $statusCode = Response::HTTP_BAD_REQUEST;
        } else {
            $foo->setVal1(true);
            $foo->setVal2(true);
            $foo->setVal3(true);
            $foo->setVal4(true);
            $foo->setVal5(true);
        }

        $foo->setUserId($user->getId());
        $foo->setUser($user);
        $em->persist($foo);
        $em->flush();

        $response = new Response();

        $response->setContent('<html><body>Code: '.$statusCode.'</body></html>');
        $response->setStatusCode($statusCode);
//        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }

    public function editAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $foo = $this->getDoctrine()
            ->getRepository('SalamonCovusBundle:Foo')
            ->find($user->getId());

        $form = $this->createForm(new AllTrueFormType(), $foo);

        $em = $this->getDoctrine()->getManager();

        $form->handleRequest($request);
        if ($form->isValid()) {
//            $form->submit($request->request->get($form->getName()));

            $em->persist($user);
            $em->flush();
        }

        return $this->container->get('templating')->renderResponse('SalamonCovusBundle:Covus:edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
