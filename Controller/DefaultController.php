<?php

namespace Stefanwiegmann\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DefaultController extends AbstractController
{
    /**
    * @Route("/user", name="user_default")
    */
    public function default()
    {
      return $this->render('@StefanwiegmannUser/default/index.html.twig', [
          'headline' => 'User Default',
          'content' => 'hello user',
      ]);
    }
}
