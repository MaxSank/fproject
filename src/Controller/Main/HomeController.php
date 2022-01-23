<?php

namespace App\Controller\Main;

use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(
        ItemRepository $itemRepository,
        EntityManagerInterface $em,
    )
    {
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Welcome!';

        $recent_items = $itemRepository->findBy([], ['createdAt' => 'DESC'], 5);
        if ($recent_items) {
            $forRender['recent_items'] = $recent_items;
        }

        $query = $em->createQuery(
            'SELECT c, u, i
                FROM App\Entity\ItemCollection c
                INNER JOIN c.items i
                INNER JOIN c.user u')
        ;
        $collections = $query->getResult();
        if ($collections) {
            $forRender['full_collections'] = $collections;
        }

        return $this->render('main/index.html.twig', $forRender);
    }
}