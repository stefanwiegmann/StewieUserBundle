<?php

namespace App\Stefanwiegmann\UserBundle\Controller\User\Edit;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Stefanwiegmann\UserBundle\Form\Type\User\DetailType;

/**
  * @IsGranted("ROLE_USER_ADMIN")
  */

class DetailController extends AbstractController
{
    /**
    * @Route("/user/user/edit/detail/{username}", name="sw_user_user_edit_detail")
    */
    public function details($username, Request $request)
    {
      //get user
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository('StefanwiegmannUserBundle:User');
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

          $this->addFlash(
            'success',
            $user->getUsername().' was updated!'
            );

          return $this->redirectToRoute('sw_user_user_edit_detail', ['username' => $user->getUsername()]);
        }

      return $this->render('@stefanwiegmann_user/user/edit/detail.html.twig', [
          'user' => $user,
          'form' => $form->createView(),
      ]);
    }
}
