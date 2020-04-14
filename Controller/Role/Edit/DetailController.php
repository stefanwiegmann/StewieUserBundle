<?php

namespace App\Stefanwiegmann\UserBundle\Controller\Role\Edit;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Stefanwiegmann\UserBundle\Form\Type\Role\DetailType;

/**
  * @IsGranted("ROLE_USER_ROLE_EDIT")
  */

class DetailController extends AbstractController
{

    /**
    * @Route("/user/role/edit/detail/{slug}", name="sw_user_role_edit_detail")
    */
    public function edit($slug, Request $request)
    {
      // get role
        $em = $this->container->get('doctrine')->getManager();
        $repository = $em->getRepository('StefanwiegmannUserBundle:Role');
        $roleObject = $repository->findOneBySlug($slug);

        // create form
        $form = $this->createForm(DetailType::class, $roleObject);

        // handle form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $roleObject = $form->getData();

            // save user
            $em->persist($roleObject);
            $em->flush();

            $this->addFlash(
              'success',
              $roleObject->getName().' was updated!'
              );

            return $this->redirectToRoute('sw_user_role_edit_detail', ['slug' => $slug]);
          }

      return $this->render('@stefanwiegmann_user/role/edit/detail.html.twig', [
          'role' => $roleObject,
          'form' => $form->createView(),
      ]);
    }
}
