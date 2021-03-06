<?php

namespace Stewie\UserBundle\Controller\Reset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Stewie\UserBundle\Form\Type\Reset\ResetType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
// use Stewie\UserBundle\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ResetController extends AbstractController
{
    /**
    * @Route("/user/reset/{token}", name="stewie_user_reset")
    */
    public function reset($token, Request $request, UserPasswordEncoderInterface $encoder)
    {
      //get user
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository('StewieUserBundle:User');
      $user = $repo->findOneByToken($token);

      // exeption if token unknown
      if(!$user){

        return $this->render('@StewieUser/reset/unknown.html.twig', [
            'token' => $token,
        ]);
      }
      
      // TODO exeption if token expired
      if(date_diff(new \DateTime("now"),$user->getTokenDate())->format('%a') > 2){

        return $this->render('@StewieUser/reset/expired.html.twig', [
            'token' => $token,
        ]);
      }

      // create form
      $form = $this->createForm(ResetType::class, $user);

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

          return $this->render('@StewieUser/reset/success.html.twig');
        }

      return $this->render('@StewieUser/reset/reset.html.twig', [
          'user' => $user,
          'form' => $form->createView(),
      ]);
    }

}
