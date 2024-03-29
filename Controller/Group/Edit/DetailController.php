<?php

namespace Stewie\UserBundle\Controller\Group\Edit;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stewie\UserBundle\Form\Type\Group\DetailType;
use Stewie\UserBundle\Service\AvatarGenerator;
use Symfony\Component\HttpFoundation\File\File;
use Stewie\UserBundle\Entity\Group;

/**
  * @IsGranted("ROLE_USER_GROUP_EDIT")
  */

class DetailController extends AbstractController
{
    /**
    * @Route("/user/group/edit/detail/{slug}", name="stewie_user_group_edit_detail")
    */
    public function details($slug, Request $request, AvatarGenerator $avatarGenerator)
    {
      //get user
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository(Group::Class);
      $group = $repo->findOneBySlug($slug);

      // create form
      $form = $this->createForm(DetailType::class, $group);

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $group = $form->getData();

          // // set avatar if old avatar was removed
          if(!$group->getAvatarFile()){
            // $avatar = new File($avatarGenerator->create($user->getUsername()));
            $group->setAvatarName($avatarGenerator->create($group));
            // $user->setAvatarSize(0);
          }

          // save user
          $em->persist($group);
          $em->flush();

          return $this->redirectToRoute('stewie_user_group_list');
        }

      return $this->render('@StewieUser/group/edit/detail.html.twig', [
          'group' => $group,
          'form' => $form->createView(),
      ]);
    }
}
