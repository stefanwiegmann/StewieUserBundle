<?php

namespace Stewie\UserBundle\Controller\User;

// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * This class provides methods which can (only) be called via AJAX from within the whole project.
 */
class AutocompleteController extends AbstractController
{
    /**
    * If the route is called via AJAX, the request is handled by the service.
    * Else, the controller redirects to the agency list.
    *
    * @Route("/user/user/autocomplete", name="stewie_user_user_autocomplete")
    */
    public function userAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {

            $qry = $request->query->get('qry');
            $em = $this->container->get('doctrine')->getManager();
            $repository = $em->getRepository('StewieUserBundle:User');
            $results = $repository->findByAnyName($qry, $this->getUser());

            $response = array("results" => $results);

            return new Response(json_encode($response));
        }
        // no AJAX: no route
        else {
            throw $this->createNotFoundException();
        }
    }
}
