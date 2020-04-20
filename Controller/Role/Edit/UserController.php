<?php

namespace App\Stefanwiegmann\UserBundle\Controller\Role\Edit;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;

/**
  * @IsGranted("ROLE_USER_ROLE_EDIT")
  */

class UserController extends AbstractController
{
    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
    * @Route("/user/role/edit/user/{slug}/{page}", defaults={"role": 0, "page": 1}
    *     , requirements={"page": "\d+"}, name="sw_user_role_edit_user")
    */
    public function user($slug, $page, Request $request)
    {
      // get filter data
      // if($role > 0){
        $repository = $this->getDoctrine()
          ->getRepository('StefanwiegmannUserBundle:Role');
        $roleObject = $repository->findOneBySlug($slug);
      // }

      //get data and paginate
      // $paginator  = $this->get('knp_paginator');
      $pagination = $this->paginator->paginate(
      $this->getQuery($roleObject), /* query NOT result */
      $request->query->getInt('page', $page)/*page number*/,
            // 10/*limit per page*/
            $this->getParameter('max_rows')/*limit per page*/
            // $this->container->getParameter('max_rows')/*limit per page*/
        );
        // $pagination->setTemplate('@SWUser/User/pagination.html.twig');
        $pagination->setTemplate('@StefanwiegmannUser/default/pagination.html.twig');

      return $this->render('@StefanwiegmannUser/role/edit/user.html.twig', [
          'role' => $roleObject,
          'userList' => $pagination,
          'page' => $page,
      ]);
    }

    public function getQuery($roleObject){

        $repository = $this->getDoctrine()
          ->getRepository('StefanwiegmannUserBundle:User');

        $query = $repository->createQueryBuilder('u')
          ->andWhere(':roles MEMBER OF u.userRoles')
          ->setParameter('roles', $roleObject)
          ->orderBy('u.id', 'ASC');

          return $query
            ->getQuery();

    }
}
