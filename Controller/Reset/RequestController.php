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
// use Stewie\UserBundle\Form\Type\Reset\ResetType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
// use Stewie\UserBundle\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class RequestController extends AbstractController
{

    /**
    * @Route("/user/request/reset", name="stewie_user_request_reset")
    */
    public function request(Request $request, MailerInterface $mailer)
    {

      // create form
      $form = $this->createFormBuilder()
        ->add('email', EmailType::class, ['label' => 'label.reset.email', 'translation_domain' => 'StewieUserBundle'])
        ->add('submit', SubmitType::class, ['label' => 'label.reset.request', 'translation_domain' => 'StewieUserBundle'])
        ->getForm();

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $em = $this->container->get('doctrine')->getManager();
          $repo = $em->getRepository('StewieUserBundle:User');

          // get form data
          $email = $form->getData();
          // dump($email);

          // get user
          $user = $repo->findOneByEmail($email);

          // set token
          $token = sha1(random_bytes(32));
          $user->setToken($token);
          $user->setTokenDate(new \DateTime("now"));

          $em->persist($user);
          $em->flush();

          // send email
          $email = (new Email())
           ->to($user->getEmail())
           // ->from($this->getParameter('stewie_user.from_email'))
           ->from(Address::create(''.$this->getParameter('stewie_user.from_name').' <'.$this->getParameter('stewie_user.from_email').'>'))
           ->subject('Your Password Request')
           ->text($this->renderView(
                       '@StewieUser/emails/request.txt.twig',
                       array('name' => $user->getFirstName().' '.$user->getLastName(),
                              'token' => $user->getToken()
                       )),
             )
           ->html($this->renderView(
                       '@StewieUser/emails/request.html.twig',
                       array('name' => $user->getFirstName().' '.$user->getLastName(),
                              'token' => $user->getToken()
                       ))
                     );

          $mailer->send($email);

          $this->addFlash(
            'success',
            'A link was emailed to '.$user->getEmail().'!'
            );

          return $this->redirectToRoute('home');
        }

      return $this->render('@StewieUser/reset/request.html.twig', [
          'form' => $form->createView(),
      ]);
    }

}
