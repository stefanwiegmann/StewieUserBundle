<?php

namespace App\Stefanwiegmann\UserBundle\Controller\Group;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Stefanwiegmann\UserBundle\Form\Type\GroupType;

/**
  * @IsGranted("ROLE_USER_ADMIN")
  */

class EditController extends AbstractController
{
    /**
    * @Route("/user/group/edit/{id}", name="sw_user_group_edit")
    */
    public function edit($id, Request $request)
    {
      //get user
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository('StefanwiegmannUserBundle:Group');
      $group = $repo->findOneById($id);

      // create form
      $form = $this->createForm(GroupType::class, $group);

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $group = $form->getData();

          // save user
          $em->persist($group);
          $em->flush();

          return $this->redirectToRoute('sw_user_group_list');
        }

      return $this->render('@stefanwiegmann_user/group/edit/edit.html.twig', [
          'group' => $group,
          'form' => $form->createView(),
      ]);
    }
}
