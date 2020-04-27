<?php

namespace Stefanwiegmann\UserBundle\Controller\Group\View;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
// use Stefanwiegmann\UserBundle\Form\Type\Group\DetailType;

/**
  * @IsGranted("ROLE_USER_GROUP_VIEW")
  */

class DetailController extends AbstractController
{
    /**
    * @Route("/user/group/view/detail/{slug}", name="sw_user_group_view_detail")
    */
    public function details($slug, Request $request)
    {
      //get user
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository('StefanwiegmannUserBundle:Group');
      $group = $repo->findOneBySlug($slug);

      return $this->render('@StefanwiegmannUser/group/view/detail.html.twig', [
          'group' => $group,
      ]);
    }
}
