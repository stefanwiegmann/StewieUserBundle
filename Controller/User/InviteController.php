<?php

namespace Stewie\UserBundle\Controller\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stewie\UserBundle\Form\Type\User\InviteType;
use Stewie\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Stewie\UserBundle\Service\AvatarGenerator;
use Symfony\Component\HttpFoundation\File\File;

class InviteController extends AbstractController
{
    /**
    * @Route("/user/invite", name="stewie_user_invite")
    */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, MailerInterface $mailer, AvatarGenerator $avatarGenerator)
    {

      //create new user
      $user = new User;

      // create form
      $form = $this->createForm(InviteType::class, $user);

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $em = $this->container->get('doctrine')->getManager();
          $user = $form->getData();

          // set default values
          $encoded = $encoder->encodePassword($user, sha1(random_bytes(5)));

          $user->setPassword($encoded);
          $user->setUsername($user->getEmail());
          $user->setInviter($this->getUser());

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
          $email = (new Email())
           ->to($user->getEmail())
           ->subject('Your Invitation')
           ->text($this->renderView(
                       '@StewieUser/emails/invitation.txt.twig', array(
                          'name' => $user->getFirstName().' '.$user->getLastName(),
                          'token' => $user->getToken(),
                          'inviter' => $this->getUser()->getUsername()
                       )),
             )
           ->html($this->renderView(
                       '@StewieUser/emails/invitation.html.twig', array(
                          'name' => $user->getFirstName().' '.$user->getLastName(),
                          'token' => $user->getToken(),
                          'inviter' => $this->getUser()->getUsername()
                       ))
                     );

          $mailer->send($email);

          $this->addFlash(
            'success',
            'A invitation was emailed to '.$user->getEmail().'!'
            );

          return $this->redirectToRoute('home');
        }

      return $this->render('@StewieUser/user/invite.html.twig', [
          'user' => $user,
          'form' => $form->createView(),
      ]);
    }

}
