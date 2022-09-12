<?php

namespace Stewie\UserBundle\Controller\Group\Edit;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;
use Stewie\UserBundle\Form\Type\User\AddUserType;
use Stewie\UserBundle\Entity\Group;
use Stewie\UserBundle\Entity\User;

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
    *     , requirements={"page": "\d+"}, name="stewie_user_group_edit_member")
    */
    public function details($slug, $page, Request $request)
    {
      //get group
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository(Group::Class);
      $group = $repo->findOneBySlug($slug);

      // create form
      $form = $this->createForm(AddUserType::class);

      // handle form
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {

          $userRepo = $em->getRepository(User::Class);
          $user = $userRepo->findOneById($form->get('userAutoId')->getData());

          // add user
          $group->addUser($user);
          $em->persist($group);
          $em->flush();

          // update roles for that user
          $userRepo->refreshRoles($user);

          $this->addFlash(
              'success',
              'User was added!'
              );

          return $this->redirectToRoute('stewie_user_group_edit_member', array('slug' => $slug));
        }

      //get data and paginate
      $pagination = $this->paginator->paginate(
      $this->getQuery($group), /* query NOT result */
      $request->query->getInt('page', $page)/*page number*/,
            // 10/*limit per page*/
            $this->getParameter('stewie_user.max_rows')/*limit per page*/
        );

        $pagination->setTemplate('@StewieUser/default/pagination.html.twig');

      return $this->render('@StewieUser/group/edit/member.html.twig', [
          'group' => $group,
          'memberList' => $pagination,
          'page' => $page,
          'form' => $form->createView(),
      ]);
    }
    public function getQuery($group){

      $repository = $this->container->get('doctrine')->getManager()
        ->getRepository(User::Class);

      $query = $repository->createQueryBuilder('u')
        ->andWhere(':groups MEMBER OF u.groups')
        ->setParameter('groups', $group)
        ->orderBy('u.id', 'ASC');

        return $query
          ->getQuery();

  }
}
