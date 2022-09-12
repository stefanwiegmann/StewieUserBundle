<?php

namespace Stewie\UserBundle\Controller\Group\Edit;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
// use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Stewie\UserBundle\Entity\Group;
use Stewie\UserBundle\Entity\User;

/**
  * @IsGranted("ROLE_USER_GROUP_EDIT")
  */

class RemoveController extends AbstractController
{
  /**
  * @Route("/user/group/remove/user/{slug}/{user}", name="stewie_user_group_remove_user")
  */
  public function userAction($slug, $user, Request $request, TranslatorInterface $translator)
  {
    //get group
    $em = $this->container->get('doctrine')->getManager();
    $groupRepo = $em->getRepository(Group::Class);
    $groupObject = $groupRepo->findOneBySlug($slug);

    //get user
    $em = $this->container->get('doctrine')->getManager();
    $userRepo = $em->getRepository(User::Class);
    $userObject = $userRepo->findOneById($user);

    // create form
    $form = $this->createFormBuilder($groupObject)
            ->add('submit', SubmitType::class, array('label' => 'label.remove',
            'translation_domain' => 'StewieUserBundle',
            'attr'=> array('class'=>'btn-danger'),))
            ->getForm();

    // handle form
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        
        // remove user from group
        $groupObject->removeUser($userObject);

        // save user
        $em->persist($groupObject);
        $em->flush();

        // update roles
        $userRepo->refreshRoles($userObject);

        return $this->redirectToRoute('stewie_user_group_edit_member', array('slug' => $slug));
      }

    return $this->render('@StewieUser/card/dangerForm.html.twig', [
        'text' => $translator->trans('confirmation.group.remove', [
          '%subject%' => $userObject->getUsername(),
          '%object%' => $groupObject->getName()
          ], 'StewieUserBundle'),
        'title' => $translator->trans('header.group.remove.user', [], 'StewieUserBundle'),
        'form' => $form->createView(),
    ]);

  }

  // /**
  // * @Route("/user/group/remove/member", name="stewie_user_group_remove_member")
  // */
  // public function memberAction(Request $request)
  // {
  //
  //   if($request->isXmlHttpRequest()) {
  //
  //     $groupId   = $request->request->get('groupId');
  //     $userId   = $request->request->get('userId');
  //
  //     $groupRepo = $this->getDoctrine()
  //       ->getRepository('StewieUserBundle:Group');
  //     $group = $groupRepo->findOneById($groupId);
  //
  //     $userRepo = $this->getDoctrine()
  //       ->getRepository('StewieUserBundle:User');
  //     $user = $userRepo->findOneById($userId);
  //
  //     $em = $this->getDoctrine()->getManager();
  //     $user->removeGroup($group);
  //     $em->persist($user);
  //     $em->flush();
  //
  //     // refresh roles for that user
  //     $userRepo->refreshRoles($user);
  //
  //     $this->addFlash(
  //       'success',
  //       $user->getUsername().' was removed from Group '.$group->getName().'!'
  //       );
  //
  //     $response = array("code" => 100, "success" => true, "groupId" => $groupId, "userId" => $userId);
  //     return new Response(json_encode($response));
  //
  //   } else {
  //     return $this->redirectToRoute('stewie_user_group_list');
  //   }
  // }

}
