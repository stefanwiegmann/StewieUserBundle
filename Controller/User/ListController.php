<?php

namespace Stewie\UserBundle\Controller\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Persistence\ManagerRegistry;
use Stewie\UserBundle\Entity\User;

/**
  * @IsGranted("ROLE_USER_USER_VIEW")
  */

class ListController extends AbstractController
{
    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
    * @Route("/user/list/{page}", defaults={"page": 1}
    *     , requirements={"page": "\d+"}, name="stewie_user_list")
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

      return $this->render('@StewieUser/user/list.html.twig', [
          'userList' => $pagination,
          'page' => $page,
          'locale' => $request->getLocale()
      ]);
    }

    public function getQuery($doctrine){

        $repository = $doctrine->getRepository(User::Class);

        $query = $repository->createQueryBuilder('u')
          ->orderBy('u.id', 'ASC');

          return $query
            ->getQuery();

    }
}
