<?php

namespace Stefanwiegmann\UserBundle\Controller\User\View;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
// use Stefanwiegmann\UserBundle\Form\Type\User\RoleType;
use Knp\Component\Pager\PaginatorInterface;

/**
  * @IsGranted("ROLE_USER_USER_VIEW")
  */

class RoleController extends AbstractController
{
    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
    * @Route("/user/user/view/role/{username}/{page}", defaults={"page": 1}
    *     , requirements={"page": "\d+"}, name="sw_user_user_view_role")
    */
    public function roles($username, $page, Request $request)
    {
      //get user
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository('StefanwiegmannUserBundle:User');
      $user = $repo->findOneByUsername($username);

      //get data and paginate
      $pagination = $this->paginator->paginate(
      $this->getQuery($user), /* query NOT result */
      $request->query->getInt('page', $page)/*page number*/,
            // 10/*limit per page*/
            $this->getParameter('max_rows')/*limit per page*/
        );
        // $pagination->setTemplate('@SWUser/User/pagination.html.twig');
        $pagination->setTemplate('@StefanwiegmannUser/default/pagination.html.twig');

      return $this->render('@StefanwiegmannUser/user/view/role.html.twig', [
          'user' => $user,
          'roleList' => $pagination,
          'page' => $page,
      ]);
    }

    public function getQuery($user){

        $repository = $this->getDoctrine()
          ->getRepository('StefanwiegmannUserBundle:Role');

        $query = $repository->createQueryBuilder('r')
          ->andWhere(':users MEMBER OF r.users')
          ->setParameter('users', $user)
          ->orderBy('r.id', 'ASC');

          return $query
            ->getQuery();

    }
}
