<?php

namespace Stewie\UserBundle\Controller\Register;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stewie\UserBundle\Form\Type\Register\RegisterType;
use Stewie\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Stewie\UserBundle\Service\AvatarGenerator;
use Symfony\Component\HttpFoundation\File\File;
use Stewie\UserBundle\Service\MailGenerator;

class RegisterController extends AbstractController
{
    /**
    * @Route("/user/register", name="stewie_user_register")
    */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, MailGenerator $mailGenerator, AvatarGenerator $avatarGenerator)
    {
      if(!$this->getParameter('stewie_user.register')){

        $this->addFlash(
          'warning',
          'Registration is disabled!'
          );

        return $this->redirectToRoute('home');
      }

      //create new user
      $user = new User;

      // create form
      $form = $this->createForm(RegisterType::class, $user);

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $em = $this->container->get('doctrine')->getManager();
          $user = $form->getData();

          // set default values
          $encoded = $encoder->encodePassword($user, sha1(random_bytes(5)));

          $user->setPassword($encoded);
          $user->setUsername($user->getEmail());

          // set token
          $token = sha1(random_bytes(32));
          $user->setToken($token);
          $user->setTokenDate(new \DateTime("now"));

          // set avatar
          $user->setAvatarName($avatarGenerator->create($user));

          // save user
          $em->persist($user);
          $em->flush();

          // send email
          $mailGenerator->register($user);

          $this->addFlash(
            'success',
            'A registration link was emailed to '.$user->getEmail().'!'
            );

          return $this->redirectToRoute('home');
        }

      return $this->render('@StewieUser/register/register.html.twig', [
          'user' => $user,
          'form' => $form->createView(),
      ]);
    }

}
