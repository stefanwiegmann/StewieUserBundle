<?php

namespace App\Stefanwiegmann\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Stefanwiegmann\UserBundle\Form\Type\ProfileType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
  * @IsGranted("IS_AUTHENTICATED_FULLY")
  */

class ProfileController extends AbstractController
{
    /**
    * @Route("/user/profile", name="sw_user_profile")
    */
    public function profile(Request $request, UserPasswordEncoderInterface $encoder)
    {
      // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

      //get user
      $em = $this->container->get('doctrine')->getManager();
      $user = $this->getUser();

      // create form
      $form = $this->createForm(ProfileType::class, $user);

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $user = $form->getData();

          $password = $form->get('password')->getData();

          $encoded = $encoder->encodePassword($user, $password);

          $user->setPassword($encoded);

          // save user
          $em->persist($user);
          $em->flush();

          return $this->redirectToRoute('sw_user_list');
        }

      return $this->render('@stefanwiegmann_user/profile/profile.html.twig', [
          'user' => $user,
          'form' => $form->createView(),
      ]);
    }
}
