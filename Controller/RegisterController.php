<?php

namespace App\Stefanwiegmann\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Stefanwiegmann\UserBundle\Form\Type\Register\RegisterType;
use App\Stefanwiegmann\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class RegisterController extends AbstractController
{
    /**
    * @Route("/user/register", name="sw_user_register")
    */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, MailerInterface $mailer)
    {
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

          // save user
          $em->persist($user);
          $em->flush();

          // send email
          $email = (new Email())
           ->to($user->getEmail())
           ->subject('Your Registration')
           ->text($this->renderView(
                       '@StefanwiegmannUser/emails/registration.txt.twig',
                       array('name' => $user->getFirstName().' '.$user->getLastName(),
                              'token' => $user->getToken()
                       )),
             )
           ->html($this->renderView(
                       '@StefanwiegmannUser/emails/registration.html.twig',
                       array('name' => $user->getFirstName().' '.$user->getLastName(),
                              'token' => $user->getToken()
                       ))
                     );

          $mailer->send($email);

          $this->addFlash(
            'success',
            'A registration link was emailed to '.$user->getEmail().'!'
            );

          return $this->redirectToRoute('home');
        }

      return $this->render('@StefanwiegmannUser/register/register.html.twig', [
          'user' => $user,
          'form' => $form->createView(),
      ]);
    }

}
