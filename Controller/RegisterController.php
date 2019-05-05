<?php

namespace App\Stefanwiegmann\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Stefanwiegmann\UserBundle\Form\Type\RegisterType;
use App\Stefanwiegmann\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends Controller
{
    /**
    * @Route("/user/register", name="sw_user_register")
    */
    public function register(Request $request)
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
          $user->setUsername($user->getEmail());
          $user->setPassword($this->passwordEncoder->encodePassword(
                       $user,
                       'password'
                   ));

          // save user
          $em->persist($user);
          $em->flush();

          return $this->redirectToRoute('sw_user_list');
        }

      return $this->render('@stefanwiegmann_user/register/register.html.twig', [
          'user' => $user,
          'form' => $form->createView(),
      ]);
    }
}
