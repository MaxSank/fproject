<?php

namespace App\Controller\User;

use App\Controller\Main\BaseController;
use Symfony\Component\Routing\Annotation\Route;

class PersonalPageController extends BaseController
{
    #[Route('/{_locale<%app.supported_locales%>}/user-{name}', name: 'user')]
    public function index(string $name)
    {
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Personal page';
        return $this->render('personalpage/index.html.twig', $forRender);

    }

}