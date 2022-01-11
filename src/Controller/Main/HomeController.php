<?php

namespace App\Controller\Main;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends BaseController
{
    #[Route('/')]
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('home', ['_locale' => 'en']);
    }


    /**
     * @Route("/{_locale<%app.supported_locales%>}/", name="home")
     */
    public function index()
    {
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Welcome!';
        return $this->render('main/index.html.twig', $forRender);
    }
}