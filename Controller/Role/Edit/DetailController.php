<?php

namespace Stewie\UserBundle\Controller\Role\Edit;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stewie\UserBundle\Form\Type\Role\DetailType;
use Stewie\UserBundle\Service\AvatarGenerator;
use Symfony\Component\HttpFoundation\File\File;

/**
  * @IsGranted("ROLE_USER_ROLE_EDIT")
  */

class DetailController extends AbstractController
{

    /**
    * @Route("/user/role/edit/detail/{slug}", name="stewie_user_role_edit_detail")
    */
    public function edit($slug, Request $request, AvatarGenerator $avatarGenerator)
    {
      // get role
        $em = $this->container->get('doctrine')->getManager();
        $repository = $em->getRepository('StewieUserBundle:Role');
        $roleObject = $repository->findOneBySlug($slug);

        // create form
        $form = $this->createForm(DetailType::class, $roleObject);

        // handle form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $roleObject = $form->getData();

            // // set avatar if old avatar was removed
            if(!$roleObject->getAvatarFile()){
              // $avatar = new File($avatarGenerator->create($user->getUsername()));
              $roleObject->setAvatarName($avatarGenerator->create($roleObject));
              // $user->setAvatarSize(0);
            }

            // save user
            $em->persist($roleObject);
            $em->flush();

            $this->addFlash(
              'success',
              $roleObject->getName().' was updated!'
              );

            return $this->redirectToRoute('stewie_user_role_edit_detail', ['slug' => $slug]);
          }

      return $this->render('@StewieUser/role/edit/detail.html.twig', [
          'role' => $roleObject,
          'form' => $form->createView(),
      ]);
    }
}
