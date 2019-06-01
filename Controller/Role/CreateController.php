<?php

namespace App\Stefanwiegmann\UserBundle\Controller\Role;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Stefanwiegmann\UserBundle\Form\Type\RoleType;
use App\Stefanwiegmann\UserBundle\Entity\Role;

/**
  * @IsGranted("ROLE_USER_ADMIN")
  */

class CreateController extends AbstractController
{
    /**
    * @Route("/user/role/create", name="sw_user_role_create")
    */
    public function create(Request $request)
    {
      //create role
      $role = new Role;

      // create form
      $form = $this->createForm(RoleType::class, $role);

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $role = $form->getData();

          // save user
          $em = $this->container->get('doctrine')->getManager();
          $em->persist($role);
          $em->flush();

          return $this->redirectToRoute('sw_user_role_list');
        }

      return $this->render('@stefanwiegmann_user/role/create/create.html.twig', [
          'role' => $role,
          'form' => $form->createView(),
      ]);
    }
}
