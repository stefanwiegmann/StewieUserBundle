<?php

namespace Stewie\UserBundle\Controller\Group\View;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
// use Stewie\UserBundle\Form\Type\Group\DetailType;
use Stewie\UserBundle\Entity\Group;

/**
  * @IsGranted("ROLE_USER_GROUP_VIEW")
  */

class DetailController extends AbstractController
{
    /**
    * @Route("/user/group/view/detail/{slug}", name="stewie_user_group_view_detail")
    */
    public function details($slug, Request $request)
    {
      //get user
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository(Group::Class);
      $group = $repo->findOneBySlug($slug);

      return $this->render('@StewieUser/group/view/detail.html.twig', [
          'group' => $group,
      ]);
    }
}
