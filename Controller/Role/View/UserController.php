<?php

namespace Stewie\UserBundle\Controller\Role\View;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;
use Stewie\UserBundle\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Stewie\UserBundle\Entity\Role;


/**
  * @IsGranted("ROLE_USER_ROLE_VIEW")
  */

class UserController extends AbstractController
{
    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
    * @Route("/user/role/view/user/{slug}/{page}", defaults={"role": 0, "page": 1}
    *     , requirements={"page": "\d+"}, name="stewie_user_role_view_user")
    */
    public function user(ManagerRegistry $doctrine, $slug, $page, Request $request)
    {
      // get filter data
      // if($role > 0){
        $repository = $doctrine->getRepository(Role::Class);
        $roleObject = $repository->findOneBySlug($slug);
      // }

      //get data and paginate
      // $paginator  = $this->get('knp_paginator');
      $pagination = $this->paginator->paginate(
      $this->getQuery($roleObject, $doctrine), /* query NOT result */
      $request->query->getInt('page', $page)/*page number*/,
            // 10/*limit per page*/
            $this->getParameter('stewie_user.max_rows')/*limit per page*/
            // $this->container->getParameter('stewie_user.max_rows')/*limit per page*/
        );
        // $pagination->setTemplate('@SWUser/User/pagination.html.twig');
        $pagination->setTemplate('@StewieUser/default/pagination.html.twig');

      return $this->render('@StewieUser/role/view/user.html.twig', [
          'role' => $roleObject,
          'userList' => $pagination,
          'page' => $page,
      ]);
    }

    public function getQuery($roleObject, $doctrine){

        $repository = $doctrine->getRepository(User::Class);

        $query = $repository->createQueryBuilder('u')
          ->andWhere(':roles MEMBER OF u.userRoles')
          ->setParameter('roles', $roleObject)
          ->orderBy('u.id', 'ASC');

          return $query
            ->getQuery();

    }
}
