<?php

namespace App\Stefanwiegmann\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Stefanwiegmann\UserBundle\Form\Type\UserType;

/**
  * @IsGranted("ROLE_USER_ADMIN")
  */

class EditController extends AbstractController
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

      // create form
      $form = $this->createForm(UserType::class, $user);

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $user = $form->getData();

          // save user
          $em->persist($user);
          $em->flush();

          return $this->redirectToRoute('sw_user_list');
        }

      return $this->render('@stefanwiegmann_user/edit/edit.html.twig', [
          'user' => $user,
          'form' => $form->createView(),
      ]);
    }
}
