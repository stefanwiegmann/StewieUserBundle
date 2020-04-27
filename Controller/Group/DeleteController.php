<?php

namespace Stefanwiegmann\UserBundle\Controller\Group;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
// use Stefanwiegmann\UserBundle\Form\Type\Group\DeleteType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
// use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
  * @IsGranted("ROLE_USER_GROUP_DELETE")
  */

class DeleteController extends AbstractController
{
  /**
  * @Route("/user/group/delete/{slug}", name="sw_user_group_delete")
  */
  public function deleteAction($slug, Request $request, TranslatorInterface $translator)
  {
    //get user
    $em = $this->container->get('doctrine')->getManager();
    $repo = $em->getRepository('StefanwiegmannUserBundle:Group');
    $group = $repo->findOneBySlug($slug);

    // create form
    // $form = $this->createForm(DeleteType::class, $group);
    $form = $this->createFormBuilder($group)
            ->add('submit', SubmitType::class, array('label' => 'label.delete',
            'translation_domain' => 'SWUserBundle',
            'attr'=> array('class'=>'btn-danger'),))
            ->getForm();

    // handle form
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $group = $form->getData();

        // save user
        $em->remove($group);
        $em->flush();

        return $this->redirectToRoute('sw_user_group_list');
      }

    return $this->render('@StefanwiegmannUser/default/remove.html.twig', [
        'title' => $translator->trans('confirmation.delete', [
          '%subject%' => $group->getName()
          ], 'SWUserBundle'),
        'text' => $translator->trans('confirmation.group.delete', [], 'SWUserBundle'),
        'header1' => $group->getName(),
        'header2' => $translator->trans('header.group.delete', [], 'SWUserBundle'),
        'form' => $form->createView(),
    ]);

  }
}
