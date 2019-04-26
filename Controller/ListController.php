<?php

namespace App\Stefanwiegmann\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ListController extends AbstractController
{
    /**
    * @Route("/user/list", name="sw_user_list")
    */
    public function list()
    {
      return $this->render('@stefanwiegmann_user/list/list.html.twig', [
          'headline' => 'User Default',
          'content' => 'hello user',
      ]);
    }
}
