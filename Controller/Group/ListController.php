<?php

namespace Stewie\UserBundle\Controller\Group;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Persistence\ManagerRegistry;
use Stewie\UserBundle\Entity\Group;

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
    *     , requirements={"page": "\d+"}, name="stewie_user_group_list")
    */
    public function list(ManagerRegistry $doctrine, $page, Request $request)
    {
      //get data and paginate
      // $paginator  = $this->get('knp_paginator');
      $pagination = $this->paginator->paginate(
      $this->getQuery($doctrine), /* query NOT result */
      $request->query->getInt('page', $page)/*page number*/,
            // 10/*limit per page*/
            $this->getParameter('stewie_user.max_rows')/*limit per page*/
            // $this->container->getParameter('stewie_user.max_rows')/*limit per page*/
        );
        // $pagination->setTemplate('@SWUser/User/pagination.html.twig');
        $pagination->setTemplate('@StewieUser/default/pagination.html.twig');

      return $this->render('@StewieUser/group/list.html.twig', [
          'groupList' => $pagination,
          'page' => $page,
      ]);
    }

    public function getQuery($doctrine){

        $repository = $doctrine->getRepository(Group::Class);

        $query = $repository->createQueryBuilder('g')
          ->orderBy('g.id', 'ASC');

          return $query
            ->getQuery();

    }
}
