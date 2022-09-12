<?php

namespace Stewie\UserBundle\Controller\User\View;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stewie\UserBundle\Entity\User;
// use Stewie\UserBundle\Form\Type\User\DetailType;

/**
  * @IsGranted("ROLE_USER_USER_VIEW")
  */

class DetailController extends AbstractController
{
    /**
    * @Route("/user/user/view/detail/{username}", name="stewie_user_user_view_detail")
    */
    public function details($username, Request $request)
    {
      //get user
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository(User::Class);
      $user = $repo->findOneByUsername($username);

      return $this->render('@StewieUser/user/view/detail.html.twig', [
          'user' => $user,
      ]);
    }
}
