<?php

namespace App\Stefanwiegmann\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
// use App\Stefanwiegmann\UserBundle\Entity\User;
// use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetController extends Controller
{
    /**
    * @Route("/user/request", name="sw_user_request")
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
          $message = (new \Swift_Message('Your Registration'))
              // ->setFrom($this->container->getParameter( 'mailer_address' ))
              ->setFrom('admin@mindpool.net')
              ->setTo($user->getEmail())
              ->setBody(
                  $this->renderView(
                      // app/Resources/views/Emails/registration.html.twig
                      '@stefanwiegmann_user/emails/request.html.twig',
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

          // $this->get('mailer')->send($message);

          // return $this->redirectToRoute('sw_user_list');
        }

      return $this->render('@stefanwiegmann_user/reset/request.html.twig', [
          'form' => $form->createView(),
      ]);
    }

}
