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
      $repo = $em->getRepository('StewieUserBundle:User');
      $user = $repo->findOneByUsername($username);

      // create form
      $form = $this->createForm(DetailType::class, $user);

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $user = $form->getData();

          // // set avatar if old avatar was removed
          if(!$user->getAvatarFile()){
            // $avatar = new File($avatarGenerator->create($user->getUsername()));
            $user->setAvatarName($avatarGenerator->create($user));
            // $user->setAvatarSize(0);
          }

          // save user
          $em->persist($user);
          $em->flush();

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
