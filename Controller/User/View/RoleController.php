<?php

namespace Stewie\UserBundle\Controller\User\View;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
// use Stewie\UserBundle\Form\Type\User\RoleType;
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
    *     , requirements={"page": "\d+"}, name="stewie_user_user_view_role")
    */
    public function roles($username, $page, Request $request)
    {
      //get user
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository('StewieUserBundle:User');
      $user = $repo->findOneByUsername($username);

      //get data and paginate
      $pagination = $this->paginator->paginate(
      $this->getQuery($user), /* query NOT result */
      $request->query->getInt('page', $page)/*page number*/,
            // 10/*limit per page*/
            $this->getParameter('stewie_user.max_rows')/*limit per page*/
        );
        // $pagination->setTemplate('@SWUser/User/pagination.html.twig');
        $pagination->setTemplate('@StewieUser/default/pagination.html.twig');

      return $this->render('@StewieUser/user/view/role.html.twig', [
          'user' => $user,
          'roleList' => $pagination,
          'page' => $page,
      ]);
    }

    public function getQuery($user){

        $repository = $this->getDoctrine()
          ->getRepository('StewieUserBundle:Role');

        $query = $repository->createQueryBuilder('r')
          ->andWhere(':users MEMBER OF r.users')
          ->setParameter('users', $user)
          ->orderBy('r.id', 'ASC');

          return $query
            ->getQuery();

    }
}
