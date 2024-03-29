<?php

namespace Stewie\UserBundle\Controller\Role\Edit;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
// use Stewie\UserBundle\Form\Type\Role\RemoveType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
// use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Stewie\UserBundle\Entity\Role;
use Stewie\UserBundle\Entity\User;
use Stewie\UserBundle\Entity\Group;

/**
  * @IsGranted("ROLE_USER_ROLE_EDIT")
  */

class RemoveController extends AbstractController
{
  /**
  * @Route("/user/role/remove/user/{role}/{user}", name="stewie_user_role_remove_user")
  */
  public function userAction($role, $user, Request $request, TranslatorInterface $translator)
  {
    //get role
    $em = $this->container->get('doctrine')->getManager();
    $roleRepo = $em->getRepository(Role::Class);
    $roleObject = $roleRepo->findOneById($role);

    //get user
    $em = $this->container->get('doctrine')->getManager();
    $userRepo = $em->getRepository(User::Class);
    $userObject = $userRepo->findOneById($user);

    // create form
    // $form = $this->createForm(DeleteType::class, $roleObject);
    $form = $this->createFormBuilder($roleObject)
            ->add('submit', SubmitType::class, array('label' => 'label.remove',
            'translation_domain' => 'StewieUserBundle',
            'attr'=> array('class'=>'btn-danger'),))
            ->getForm();

    // handle form
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // remove userRole
        $userObject->removeUserRoles($roleObject);

        // save user
        $em->persist($userObject);
        $em->flush();

        // update roles
        $userRepo->refreshRoles($userObject);

        return $this->redirectToRoute('stewie_user_role_edit_user', array('slug' => $roleObject->getSlug()));
      }

    return $this->render('@StewieUser/card/dangerForm.html.twig', [
        'text' => $translator->trans('confirmation.role.remove', [
          '%subject%' => $userObject->getUsername(),
          '%object%' => $translator->trans($roleObject->getTranslationKey(), [], 'Roles')
          ], 'StewieUserBundle'),
        'title' => $translator->trans('header.role.remove', [], 'StewieUserBundle'),
        'form' => $form->createView(),
    ]);

  }

    /**
    * @Route("/user/role/remove/group/{role}/{group}", name="stewie_user_role_remove_group")
    */
    public function groupAction($role, $group, Request $request, TranslatorInterface $translator)
    {
      //get role
      $em = $this->container->get('doctrine')->getManager();
      $roleRepo = $em->getRepository(Role::Class);
      $roleObject = $roleRepo->findOneById($role);

      //get user
      $em = $this->container->get('doctrine')->getManager();
      $groupRepo = $em->getRepository(Group::Class);
      $groupObject = $groupRepo->findOneById($group);

      // create form
      // $form = $this->createForm(DeleteType::class, $roleObject);
      $form = $this->createFormBuilder($roleObject)
              ->add('submit', SubmitType::class, array('label' => 'label.remove',
              'translation_domain' => 'StewieUserBundle',
              'attr'=> array('class'=>'btn-danger'),))
              ->getForm();

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          // remove userRole
          $groupObject->removeGroupRole($roleObject);

          // save user
          $em->persist($groupObject);
          $em->flush();

          // update roles
          $groupRepo->refreshRoles($groupObject);

          return $this->redirectToRoute('stewie_user_role_edit_group', array('slug' => $roleObject->getSlug()));
        }

    return $this->render('@StewieUser/card/dangerForm.html.twig', [
        'text' => $translator->trans('confirmation.role.remove', [
          '%subject%' => $groupObject->getName(),
          '%object%' => $translator->trans($roleObject->getTranslationKey(), [], 'Roles')
          ], 'StewieUserBundle'),
        'title' => $translator->trans($roleObject->getTranslationKey(), [], 'Roles'),
        'mutedTitle' => $translator->trans('header.role.remove', [], 'StewieUserBundle'),
        'form' => $form->createView(),
    ]);

    }

}
