<?php

namespace App\Stefanwiegmann\UserBundle\Controller\Group;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Stefanwiegmann\UserBundle\Form\Type\Group\CreateType;
use App\Stefanwiegmann\UserBundle\Entity\Group;

/**
  * @IsGranted("ROLE_USER_GROUP_CREATE")
  */

class CreateController extends AbstractController
{
    /**
    * @Route("/user/group/create", name="sw_user_group_create")
    */
    public function create(Request $request)
    {
      //create group
      $group = new Group;

      // create form
      $form = $this->createForm(CreateType::class, $group);

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $group = $form->getData();

          // save user
          $em = $this->container->get('doctrine')->getManager();
          $em->persist($group);
          $em->flush();

          return $this->redirectToRoute('sw_user_group_list');
        }

      return $this->render('@StefanwiegmannUser/group/create.html.twig', [
          'group' => $group,
          'form' => $form->createView(),
      ]);
    }
}
