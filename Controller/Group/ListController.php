<?php

namespace App\Stefanwiegmann\UserBundle\Controller\Group;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;

/**
  * @IsGranted("ROLE_USER_VIEW")
  */

class ListController extends AbstractController
{
    /**
    * @Route("/user/group/list/{page}", defaults={"page": 1}
    *     , requirements={"page": "\d+"}, name="sw_user_group_list")
    */
    public function list($page, Request $request)
    {
      //get data and paginate
      $paginator  = $this->get('knp_paginator');
      $pagination = $paginator->paginate(
      $this->getQuery(), /* query NOT result */
      $request->query->getInt('page', $page)/*page number*/,
            $this->container->getParameter('max_rows')/*limit per page*/
        );
        // $pagination->setTemplate('@SWUser/User/pagination.html.twig');
        $pagination->setTemplate('@stefanwiegmann_user/default/pagination.html.twig');

      return $this->render('@stefanwiegmann_user/group/list/list.html.twig', [
          'groupList' => $pagination,
          'page' => $page,
      ]);
    }

    public function getQuery(){

        $repository = $this->getDoctrine()
          ->getRepository('StefanwiegmannUserBundle:Group');

        $query = $repository->createQueryBuilder('g')
          ->orderBy('g.id', 'ASC');

          return $query
            ->getQuery();

    }
}
