<?php

namespace Stewie\UserBundle\Controller\Role\View;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Persistence\ManagerRegistry;
use Stewie\UserBundle\Entity\Role;

/**
  * @IsGranted("ROLE_USER_ROLE_VIEW")
  */

class DetailController extends AbstractController
{

    /**
    * @Route("/user/role/view/detail/{slug}", name="stewie_user_role_view_detail")
    */
    public function view(ManagerRegistry $doctrine, $slug, Request $request)
    {
      // get role
        $repository = $doctrine->getRepository(Role::Class);
        $roleObject = $repository->findOneBySlug($slug);

      return $this->render('@StewieUser/role/view/detail.html.twig', [
          'role' => $roleObject,
      ]);
    }
}
