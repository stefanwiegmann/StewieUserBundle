<?php

namespace Stewie\UserBundle\Controller\Role\View;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;

/**
  * @IsGranted("ROLE_USER_ROLE_VIEW")
  */

class GroupController extends AbstractController
{
    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
    * @Route("/user/role/view/group/{slug}/{page}", defaults={"role": 0, "page": 1}
    *     , requirements={"page": "\d+"}, name="sw_user_role_view_group")
    */
    public function list($slug, $page, Request $request)
    {
      // get filter data
      // if($role > 0){
        $repository = $this->getDoctrine()
          ->getRepository('StewieUserBundle:Role');
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

      return $this->render('@StefanwiegmannUser/role/view/group.html.twig', [
          'role' => $roleObject,
          'groupList' => $pagination,
          'page' => $page,
      ]);
    }

    public function getQuery($roleObject){

        $repository = $this->getDoctrine()
          ->getRepository('StewieUserBundle:Group');

        $query = $repository->createQueryBuilder('g')
          ->andWhere(':roles MEMBER OF g.groupRoles')
          ->setParameter('roles', $roleObject)
          ->orderBy('g.id', 'ASC');

          return $query
            ->getQuery();

    }
}
