<?php

namespace Stewie\UserBundle\Controller\User\Edit;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stewie\UserBundle\Form\Type\User\DetailType;
use Stewie\UserBundle\Service\AvatarGenerator;
use Symfony\Component\HttpFoundation\File\File;
use Stewie\UserBundle\Entity\User;

/**
  * @IsGranted("ROLE_USER_USER_EDIT")
  */

class DetailController extends AbstractController
{
    /**
    * @Route("/user/user/edit/detail/{username}", name="stewie_user_user_edit_detail")
    */
    public function details($username, Request $request, AvatarGenerator $avatarGenerator)
    {

      //get user
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository(User::Class);
      $user = $repo->findOneByUsername($username);

      // create form
      $form = $this->createForm(DetailType::class, $user);

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $user = $form->getData();

          // save user
          $em->persist($user);
          $em->flush();

          // set null to avoid error on serialize
          $user->setAvatarFile(null);

          $this->addFlash(
            'success',
            $user->getUsername().' was updated!'
            );

          return $this->redirectToRoute('stewie_user_user_edit_detail', ['username' => $user->getUsername()]);
        }

      return $this->render('@StewieUser/user/edit/detail.html.twig', [
          'user' => $user,
          'form' => $form->createView(),
      ]);
    }
}
