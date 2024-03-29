<?php

namespace Stewie\UserBundle\Controller\User\View;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
// use Stewie\UserBundle\Form\Type\User\GroupType;
use Knp\Component\Pager\PaginatorInterface;
use Stewie\UserBundle\Entity\User;
use Stewie\UserBundle\Entity\Group;

/**
  * @IsGranted("ROLE_USER_USER_VIEW")
  */

class GroupController extends AbstractController
{
    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
    * @Route("/user/user/view/group/{username}/{page}", defaults={"page": 1}
    *     , requirements={"page": "\d+"}, name="stewie_user_user_view_group")
    */
    public function roles($username, $page, Request $request)
    {
      //get user
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository(User::Class);
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

      return $this->render('@StewieUser/user/view/group.html.twig', [
          'user' => $user,
          'groupList' => $pagination,
          'page' => $page,
      ]);
    }

    public function getQuery($user){

        $repository = $this->getDoctrine()
          ->getRepository(Group::Class);

        $query = $repository->createQueryBuilder('g')
          ->andWhere(':users MEMBER OF g.users')
          ->setParameter('users', $user)
          ->orderBy('g.id', 'ASC');

          return $query
            ->getQuery();

    }
}
