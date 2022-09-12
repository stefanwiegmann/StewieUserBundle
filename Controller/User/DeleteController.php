<?php

namespace Stewie\UserBundle\Controller\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
// use Stewie\WikiBundle\Form\Type\Group\DeleteType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
// use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
  * @IsGranted("ROLE_USER_USER_DELETE")
  */

class DeleteController extends AbstractController
{
  /**
  * @Route("/user/user/delete/{username}", name="stewie_user_user_delete")
  */
  public function deleteAction($username, Request $request, TranslatorInterface $translator)
  {
    //get user
    $em = $this->container->get('doctrine')->getManager();
    $repo = $em->getRepository(User::Class);
    $user = $repo->findOneByUsername($username);

    // create form
    $form = $this->createFormBuilder($user)

            ->add('submit', SubmitType::class, array(
                'label' => 'label.delete',
                'translation_domain' => 'StewieUserBundle',
                'attr'=> array('class'=>'btn-danger'),
            ))

            ->getForm();

    // handle form
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user = $form->getData();

        // save user
        $em->remove($user);
        $em->flush();

        $this->addFlash(
            'success',
            'User was deleted!'
            );

        return $this->redirectToRoute('stewie_user_list');
      }

    return $this->render('@StewieUser/card/dangerForm.html.twig', [
        'title' => $translator->trans('title.user.delete', [], 'StewieUserBundle'),
        'text' => $translator->trans('text.user.delete', [
            '%subject%' => $user->getUsername()
            ], 'StewieUserBundle'),
        'user' => $user,
        'form' => $form->createView(),
    ]);

  }
}
