<?php

namespace App\Controller\Main;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PersonalAccountController extends BaseController
{
    #[Route('/user', name: 'user')]
    public function index()
    {
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Personal page';
        return $this->render('personalpage/index.html.twig', $forRender);

    }

}