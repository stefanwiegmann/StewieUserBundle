<?php

namespace App\Stefanwiegmann\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Stefanwiegmann\UserBundle\Form\Type\RegisterType;
use App\Stefanwiegmann\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
    * @Route("/user/register", name="sw_user_register")
    */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
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
          $encoded = $encoder->encodePassword($user, 'dgehdyrhfuiykhju');

          $user->setPassword($encoded);
          $user->setUsername($user->getEmail());

          // save user
          $em->persist($user);
          $em->flush();

          // send email
          $message = (new \Swift_Message('Your Registration'))
              // ->setFrom($this->container->getParameter( 'mailer_address' ))
              ->setFrom('admin@mindpool.net')
              ->setTo($user->getEmail())
              ->setBody(
                  $this->renderView(
                      // app/Resources/views/Emails/registration.html.twig
                      '@stefanwiegmann_user/emails/registration.html.twig',
                      array('name' => $user->getFirstName().' '.$user->getLastName()
                      )),
                  'text/html'
              )
              //  If you also want to include a plaintext version of the message
              // ->addPart(
              //     $this->renderView(
              //         '@stefanwiegmann_user/emails/registration.txt.twig',
              //         array('name' => $user->getFirstName().' '.$user->getLastName()
              //         )),
              //     'text/plain'
              // )
          ;

          $this->get('mailer')->send($message);

          // return $this->redirectToRoute('sw_user_list');
        }

      return $this->render('@stefanwiegmann_user/register/register.html.twig', [
          'user' => $user,
          'form' => $form->createView(),
      ]);
    }

}
