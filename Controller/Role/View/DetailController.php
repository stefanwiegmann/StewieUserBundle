<?php

namespace App\Stefanwiegmann\UserBundle\Controller\Role\View;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
  * @IsGranted("ROLE_USER_ROLE_VIEW")
  */

class DetailController extends AbstractController
{

    /**
    * @Route("/user/role/view/detail/{slug}", name="sw_user_role_view_detail")
    */
    public function view($slug, Request $request)
    {
      // get role
        $repository = $this->getDoctrine()
          ->getRepository('StefanwiegmannUserBundle:Role');
        $roleObject = $repository->findOneBySlug($slug);

      return $this->render('@StefanwiegmannUser/role/view/detail.html.twig', [
          'role' => $roleObject,
      ]);
    }
}
