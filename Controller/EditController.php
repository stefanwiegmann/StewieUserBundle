<?php

namespace App\Stefanwiegmann\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
  * @IsGranted("ROLE_USER_ADMIN")
  */

class EditController extends Controller
{
    /**
    * @Route("/user/edit/{id}", name="sw_user_edit")
    */
    public function list($id, Request $request)
    {
      //get user
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository('StefanwiegmannUserBundle:User');
      $user = $repo->findOneById($id);

      return $this->render('@stefanwiegmann_user/edit/edit.html.twig', [
          'user' => $user,
      ]);
    }
}
