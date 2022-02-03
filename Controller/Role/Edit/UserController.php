<?php

namespace Stewie\UserBundle\Controller\Role\Edit;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;
use Stewie\UserBundle\Form\Type\User\AddUserType;

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
    *     , requirements={"page": "\d+"}, name="stewie_user_role_edit_user")
    */
    public function user($slug, $page, Request $request)
    {
      // get filter data
      // if($role > 0){
      $em = $this->container->get('doctrine')->getManager();
      $repository = $em->getRepository('StewieUserBundle:Role');
      $roleObject = $repository->findOneBySlug($slug);
      // }

      // create form
      $form = $this->createForm(AddUserType::class);

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {

          $userRepo = $em->getRepository('StewieUserBundle:User');
          $user = $userRepo->findOneById($form->get('userAutoId')->getData());

          // add user
          $roleObject->addUser($user);
          $em->persist($roleObject);
          $em->flush();

          // update roles for that user
          $userRepo->refreshRoles($user);

          $this->addFlash(
              'success',
              'User was added!'
              );

          return $this->redirectToRoute('stewie_user_role_edit_user', array('slug' => $slug));
        }

      //get data and paginate
      // $paginator  = $this->get('knp_paginator');
      $pagination = $this->paginator->paginate(
      $this->getQuery($roleObject), /* query NOT result */
      $request->query->getInt('page', $page)/*page number*/,
            // 10/*limit per page*/
            $this->getParameter('stewie_user.max_rows')/*limit per page*/
            // $this->container->getParameter('stewie_user.max_rows')/*limit per page*/
        );
        // $pagination->setTemplate('@SWUser/User/pagination.html.twig');
        $pagination->setTemplate('@StewieUser/default/pagination.html.twig');

      return $this->render('@StewieUser/role/edit/user.html.twig', [
          'role' => $roleObject,
          'userList' => $pagination,
          'page' => $page,
          'form' => $form->createView(),
      ]);
    }

    public function getQuery($roleObject){

        $repository = $this->getDoctrine()
          ->getRepository('StewieUserBundle:User');

        $query = $repository->createQueryBuilder('u')
          ->andWhere(':roles MEMBER OF u.userRoles')
          ->setParameter('roles', $roleObject)
          ->orderBy('u.id', 'ASC');

          return $query
            ->getQuery();

    }
}
