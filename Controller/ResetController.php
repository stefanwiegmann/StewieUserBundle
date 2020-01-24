<?php

namespace App\Stefanwiegmann\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Stefanwiegmann\UserBundle\Form\Type\ResetType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
// use App\Stefanwiegmann\UserBundle\Entity\User;

class ResetController extends AbstractController
{
    /**
    * @Route("/user/reset/{token}", name="sw_user_reset")
    */
    public function reset($token, Request $request, UserPasswordEncoderInterface $encoder)
    {
      //get user
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository('StefanwiegmannUserBundle:User');
      $user = $repo->findOneByToken($token);

      // exeption if token unknown
      if(!$user){

        return $this->render('@stefanwiegmann_user/reset/unknown.html.twig', [
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

          return $this->render('@stefanwiegmann_user/reset/success.html.twig');
        }

      return $this->render('@stefanwiegmann_user/reset/reset.html.twig', [
          'user' => $user,
          'form' => $form->createView(),
      ]);
    }

    /**
    * @Route("/user/request/reset", name="sw_user_request_reset")
    */
    public function request(Request $request)
    {

      // create form
      $form = $this->createFormBuilder()
        ->add('email', EmailType::class)
        ->add('submit', SubmitType::class, ['label' => 'Reset Password'])
        ->getForm();

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $em = $this->container->get('doctrine')->getManager();
          $repo = $em->getRepository('StefanwiegmannUserBundle:User');

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
          $message = (new \Swift_Message('Your Password Request'))
              // ->setFrom($this->container->getParameter( 'mailer_address' ))
              ->setFrom('admin@mindpool.net')
              ->setTo($user->getEmail())
              ->setBody(
                  $this->renderView(
                      '@stefanwiegmann_user/emails/request.html.twig',
                      array('name' => $user->getFirstName().' '.$user->getLastName()
                      )),
                  'text/html'
              )
              //  If you also want to include a plaintext version of the message
              ->addPart(
                  $this->renderView(
                      '@stefanwiegmann_user/emails/request.txt.twig',
                      array('name' => $user->getFirstName().' '.$user->getLastName()
                      )),
                  'text/plain'
              )
          ;

          $this->get('mailer')->send($message);

          // return $this->redirectToRoute('sw_user_list');
        }

      return $this->render('@stefanwiegmann_user/reset/request.html.twig', [
          'form' => $form->createView(),
      ]);
    }

}
