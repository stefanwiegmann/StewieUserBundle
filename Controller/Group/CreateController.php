<?php

namespace Stefanwiegmann\UserBundle\Controller\Group;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stefanwiegmann\UserBundle\Form\Type\Group\CreateType;
use Stefanwiegmann\UserBundle\Entity\Group;
use Stefanwiegmann\UserBundle\Service\AvatarGenerator;
use Symfony\Component\HttpFoundation\File\File;

/**
  * @IsGranted("ROLE_USER_GROUP_CREATE")
  */

class CreateController extends AbstractController
{
    /**
    * @Route("/user/group/create", name="sw_user_group_create")
    */
    public function create(Request $request, AvatarGenerator $avatarGenerator)
    {
      //create group
      $group = new Group;

      // create form
      $form = $this->createForm(CreateType::class, $group);

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $group = $form->getData();

          // set avatar
          $group->setAvatarName($avatarGenerator->create($group));

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
