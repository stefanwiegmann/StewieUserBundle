<?php

namespace App\Stefanwiegmann\UserBundle\Controller\Role\Edit;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
// use App\Stefanwiegmann\UserBundle\Form\Type\Role\RemoveType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
// use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
  * @IsGranted("ROLE_USER_ROLE_EDIT")
  */

class RemoveController extends AbstractController
{
  /**
  * @Route("/user/role/remove/user/{role}/{user}", name="sw_user_role_remove_user")
  */
  public function userAction($role, $user, Request $request, TranslatorInterface $translator)
  {
    //get role
    $em = $this->container->get('doctrine')->getManager();
    $roleRepo = $em->getRepository('StefanwiegmannUserBundle:Role');
    $roleObject = $roleRepo->findOneById($role);

    //get user
    $em = $this->container->get('doctrine')->getManager();
    $userRepo = $em->getRepository('StefanwiegmannUserBundle:User');
    $userObject = $userRepo->findOneById($user);

    // create form
    // $form = $this->createForm(DeleteType::class, $roleObject);
    $form = $this->createFormBuilder($roleObject)
            ->add('submit', SubmitType::class, array('label' => 'label.remove',
            'translation_domain' => 'SWUserBundle',
            'attr'=> array('class'=>'btn-danger'),))
            ->getForm();

    // handle form
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // remove userRole
        $userObject->removeUserRole($roleObject);

        // save user
        $em->persist($userObject);
        $em->flush();

        // update roles
        $userRepo->refreshRoles($userObject);

        return $this->redirectToRoute('sw_user_role_edit_user', array('slug' => $roleObject->getSlug()));
      }

    return $this->render('@StefanwiegmannUser/card/dangerForm.html.twig', [
        'text' => $translator->trans('confirmation.role.remove', [
          '%subject%' => $userObject->getUsername(),
          '%object%' => $translator->trans($roleObject->getTranslationKey(), [], 'Roles')
          ], 'SWUserBundle'),
        'title' => $translator->trans($roleObject->getTranslationKey(), [], 'Roles'),
        'mutedTitle' => $translator->trans('header.role.remove', [], 'SWUserBundle'),
        'form' => $form->createView(),
    ]);

  }

    /**
    * @Route("/user/role/remove/group/{role}/{group}", name="sw_user_role_remove_group")
    */
    public function groupAction($role, $group, Request $request, TranslatorInterface $translator)
    {
      //get role
      $em = $this->container->get('doctrine')->getManager();
      $roleRepo = $em->getRepository('StefanwiegmannUserBundle:Role');
      $roleObject = $roleRepo->findOneById($role);

      //get user
      $em = $this->container->get('doctrine')->getManager();
      $groupRepo = $em->getRepository('StefanwiegmannUserBundle:Group');
      $groupObject = $groupRepo->findOneById($group);

      // create form
      // $form = $this->createForm(DeleteType::class, $roleObject);
      $form = $this->createFormBuilder($roleObject)
              ->add('submit', SubmitType::class, array('label' => 'label.remove',
              'translation_domain' => 'SWUserBundle',
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

          return $this->redirectToRoute('sw_user_role_edit_group', array('slug' => $roleObject->getSlug()));
        }

    return $this->render('@StefanwiegmannUser/card/dangerForm.html.twig', [
        'text' => $translator->trans('confirmation.role.remove', [
          '%subject%' => $groupObject->getName(),
          '%object%' => $translator->trans($roleObject->getTranslationKey(), [], 'Roles')
          ], 'SWUserBundle'),
        'title' => $translator->trans($roleObject->getTranslationKey(), [], 'Roles'),
        'mutedTitle' => $translator->trans('header.role.remove', [], 'SWUserBundle'),
        'form' => $form->createView(),
    ]);

    }

}
