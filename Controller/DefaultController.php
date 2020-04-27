<?php

namespace Stewie\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DefaultController extends AbstractController
{
    /**
    * @Route("/user", name="stewie_user_default")
    */
    public function default()
    {
      return $this->render('@StewieUser/default/index.html.twig', [
          'headline' => 'User Default',
          'content' => 'hello user',
      ]);
    }
}
