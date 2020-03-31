<?php

namespace App\Stefanwiegmann\UserBundle\Controller\Group;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
// use Symfony\Component\HttpFoundation\JsonResponse;

/**
  * @IsGranted("ROLE_USER_ADMIN")
  */

class RemoveController extends AbstractController
{
  /**
  * @Route("/user/group/remove/member", name="sw_user_group_remove_member")
  */
  public function groupAction(Request $request)
  {

    if($request->isXmlHttpRequest()) {

      $groupId   = $request->request->get('groupId');
      $userId   = $request->request->get('userId');

      $groupRepo = $this->getDoctrine()
        ->getRepository('StefanwiegmannUserBundle:Group');
      $group = $groupRepo->findOneById($groupId);

      $userRepo = $this->getDoctrine()
        ->getRepository('StefanwiegmannUserBundle:User');
      $user = $userRepo->findOneById($userId);

      $em = $this->getDoctrine()->getManager();
      $user->removeGroup($group);
      $em->persist($user);
      $em->flush();

      // refresh roles for that user
      $userRepo->refreshRoles($user);

      $this->addFlash(
        'success',
        $user->getUsername().' was removed from Group '.$group->getName().'!'
        );

      $response = array("code" => 100, "success" => true, "groupId" => $groupId, "userId" => $userId);
      return new Response(json_encode($response));

    } else {
      return $this->redirectToRoute('sw_user_group_list');
    }
  }

}
