<?php

namespace App\Stefanwiegmann\UserBundle\Controller\Role;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Stefanwiegmann\UserBundle\Form\Type\RoleType;

/**
  * @IsGranted("ROLE_USER_ADMIN")
  */

class EditController extends AbstractController
{
    /**
    * @Route("/user/role/edit/{id}", name="sw_user_role_edit")
    */
    public function edit($id, Request $request)
    {
      //get user
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository('StefanwiegmannUserBundle:Role');
      $role = $repo->findOneById($id);

      // create form
      $form = $this->createForm(RoleType::class, $role);

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $role = $form->getData();

          // save role
          $em->persist($role);
          $em->flush();

          return $this->redirectToRoute('sw_user_role_list');
        }

      return $this->render('@stefanwiegmann_user/role/edit/edit.html.twig', [
          'role' => $role,
          'form' => $form->createView(),
      ]);
    }
}
