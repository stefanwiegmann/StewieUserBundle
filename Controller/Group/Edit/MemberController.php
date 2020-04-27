<?php

namespace Stewie\UserBundle\Controller\Group\Edit;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;

/**
  * @IsGranted("ROLE_USER_GROUP_EDIT")
  */

class MemberController extends AbstractController
{
    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
    * @Route("/user/group/edit/member/{slug}/{page}", defaults={"page": 1}
    *     , requirements={"page": "\d+"}, name="sw_user_group_edit_member")
    */
    public function details($slug, $page, Request $request)
    {
      //get group
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository('StewieUserBundle:Group');
      $group = $repo->findOneBySlug($slug);

      //get data and paginate
      $pagination = $this->paginator->paginate(
      $this->getQuery($group), /* query NOT result */
      $request->query->getInt('page', $page)/*page number*/,
            // 10/*limit per page*/
            $this->getParameter('max_rows')/*limit per page*/
        );
        // $pagination->setTemplate('@SWUser/User/pagination.html.twig');
        $pagination->setTemplate('@StefanwiegmannUser/default/pagination.html.twig');

      return $this->render('@StefanwiegmannUser/group/edit/member.html.twig', [
          'group' => $group,
          'memberList' => $pagination,
          'page' => $page,
      ]);
    }

    public function getQuery($group){

        $repository = $this->getDoctrine()
          ->getRepository('StewieUserBundle:User');

        $query = $repository->createQueryBuilder('u')
          ->andWhere(':groups MEMBER OF u.groups')
          ->setParameter('groups', $group)
          ->orderBy('u.id', 'ASC');

          return $query
            ->getQuery();

    }
}
