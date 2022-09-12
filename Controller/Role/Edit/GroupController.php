<?php

namespace Stewie\UserBundle\Controller\Role\Edit;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stewie\UserBundle\Form\Type\Role\GroupType;
use Stewie\UserBundle\Entity\Role;
use Stewie\UserBundle\Entity\Group;

/**
  * @IsGranted("ROLE_USER_ROLE_EDIT")
  */

class GroupController extends AbstractController
{
    /**
    * @Route("/user/role/edit/group/{slug}", name="stewie_user_role_edit_group")
    */
    public function groups($slug, Request $request)
    {
      //get user
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository(Role::Class);
      $role = $repo->findOneBySlug($slug);

      // create form
      $form = $this->createForm(GroupType::class, $role);

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $role = $form->getData();

          // update groups
          $groupRepo = $em->getRepository(Group::Class);
          $groupRepo->updateRole($role);

          // save user
          $em->persist($role);
          $em->flush();

          // update affected user roles
          // $repo->refreshRoles($group);

          return $this->redirectToRoute('stewie_user_role_edit_group', ['slug' => $slug]);
        }

      return $this->render('@StewieUser/role/edit/group.html.twig', [
          'role' => $role,
          'form' => $form->createView(),
      ]);
    }

    //
    // public function list($slug, Request $request)
    // {
    //   // get filter data
    //   // if($role > 0){
    //     $repository = $this->getDoctrine()
    //       ->getRepository('StewieUserBundle:Role');
    //     $roleObject = $repository->findOneBySlug($slug);
    //   // }
    //
    //   //get data and paginate
    //   // $paginator  = $this->get('knp_paginator');
    //   $pagination = $this->paginator->paginate(
    //   $this->getQuery($roleObject), /* query NOT result */
    //   $request->query->getInt('page', $page)/*page number*/,
    //         // 10/*limit per page*/
    //         $this->getParameter('stewie_user.max_rows')/*limit per page*/
    //         // $this->container->getParameter('stewie_user.max_rows')/*limit per page*/
    //     );
    //     // $pagination->setTemplate('@SWUser/User/pagination.html.twig');
    //     $pagination->setTemplate('@StewieUser/default/pagination.html.twig');
    //
    //   return $this->render('@StewieUser/role/edit/group.html.twig', [
    //       'role' => $roleObject,
    //       'groupList' => $pagination,
    //       'page' => $page,
    //   ]);
    // }
    //
    // public function getQuery($roleObject){
    //
    //     $repository = $this->getDoctrine()
    //       ->getRepository('StewieUserBundle:Group');
    //
    //     $query = $repository->createQueryBuilder('g')
    //       ->andWhere(':roles MEMBER OF g.groupRoles')
    //       ->setParameter('roles', $roleObject)
    //       ->orderBy('g.id', 'ASC');
    //
    //       return $query
    //         ->getQuery();
    //
    // }
}
