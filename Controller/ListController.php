<?php

namespace App\Stefanwiegmann\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ListController extends Controller
{
    /**
    * @Route("/user/list/{page}", defaults={"page": 1}
    *     , requirements={"page": "\d+"}, name="sw_user_list")
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
        // $pagination->setTemplate('@SWUser/User/pagination.html.twig');

      return $this->render('@stefanwiegmann_user/list/list.html.twig', [
          'userList' => $pagination,
          'page' => $page,
      ]);
    }

    public function getQuery(){

        $repository = $this->getDoctrine()
          ->getRepository('StefanwiegmannUserBundle:User');

        $query = $repository->createQueryBuilder('u')
          ->orderBy('u.id', 'ASC');

          return $query
            ->getQuery();

    }
}
