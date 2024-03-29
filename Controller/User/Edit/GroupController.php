<?php

namespace Stewie\UserBundle\Controller\User\Edit;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stewie\UserBundle\Form\Type\User\GroupType;
use Stewie\UserBundle\Entity\User;

/**
  * @IsGranted("ROLE_USER_USER_EDIT")
  */

class GroupController extends AbstractController
{
    /**
    * @Route("/user/user/edit/group/{username}", name="stewie_user_user_edit_group")
    */
    public function groups($username, Request $request)
    {
      //get user
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository(User::Class);
      $user = $repo->findOneByUsername($username);

      // create form
      $form = $this->createForm(GroupType::class, $user);

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $user = $form->getData();

          // update groups
          $groupRepo = $em->getRepository('StewieUserBundle:Group');
          $groupRepo->updateUser($user);

          // save user
          $em->persist($user);
          $em->flush();

          // update affected user roles
          $repo->refreshRoles($user);

          return $this->redirectToRoute('stewie_user_user_edit_group', ['username' => $user->getUsername()]);
        }

      return $this->render('@StewieUser/user/edit/group.html.twig', [
          'user' => $user,
          'form' => $form->createView(),
      ]);
    }
}
