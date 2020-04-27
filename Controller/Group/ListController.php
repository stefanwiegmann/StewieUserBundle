<?php

namespace Stewie\UserBundle\Controller\Group;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;

/**
  * @IsGranted("ROLE_USER_GROUP_VIEW")
  */

class ListController extends AbstractController
{
    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
    * @Route("/user/group/list/{page}", defaults={"page": 1}
    *     , requirements={"page": "\d+"}, name="sw_user_group_list")
    */
    public function list($page, Request $request)
    {
      //get data and paginate
      // $paginator  = $this->get('knp_paginator');
      $pagination = $this->paginator->paginate(
      $this->getQuery(), /* query NOT result */
      $request->query->getInt('page', $page)/*page number*/,
            // 10/*limit per page*/
            $this->getParameter('max_rows')/*limit per page*/
            // $this->container->getParameter('max_rows')/*limit per page*/
        );
        // $pagination->setTemplate('@SWUser/User/pagination.html.twig');
        $pagination->setTemplate('@StefanwiegmannUser/default/pagination.html.twig');

      return $this->render('@StefanwiegmannUser/group/list.html.twig', [
          'groupList' => $pagination,
          'page' => $page,
      ]);
    }

    public function getQuery(){

        $repository = $this->getDoctrine()
          ->getRepository('StewieUserBundle:Group');

        $query = $repository->createQueryBuilder('g')
          ->orderBy('g.id', 'ASC');

          return $query
            ->getQuery();

    }
}
