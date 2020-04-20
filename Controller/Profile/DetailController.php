<?php

namespace App\Stefanwiegmann\UserBundle\Controller\Profile;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Stefanwiegmann\UserBundle\Form\Type\Profile\DetailType;
// use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
  * @IsGranted("IS_AUTHENTICATED_FULLY")
  */

class DetailController extends AbstractController
{
    /**
    * @Route("/user/profile", name="sw_user_profile")
    */
    public function profile(Request $request)
    {
      // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

      //get user
      $em = $this->container->get('doctrine')->getManager();
      $user = $this->getUser();

      // create form
      $form = $this->createForm(DetailType::class, $user);

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $user = $form->getData();

          // $password = $form->get('password')->getData();
          //
          // $encoded = $encoder->encodePassword($user, $password);
          //
          // $user->setPassword($encoded);

          // save user
          $em->persist($user);
          $em->flush();

          $this->addFlash(
            'success',
            'Your profile was updated!'
            );

          return $this->redirectToRoute('sw_user_profile');
        }

      return $this->render('@StefanwiegmannUser/profile/detail.html.twig', [
          'user' => $user,
          'form' => $form->createView(),
      ]);
    }
}
