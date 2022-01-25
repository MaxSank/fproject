<?php

namespace App\Controller\Main;

use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HomeController extends BaseController
{

    #[Route('/')]
    public function indexNoLocale(TokenStorageInterface $tokenStorage): Response
    {
        if (!$token = $tokenStorage->getToken()) {
            $language = 'en';
        } else {
            $language = $token->getUser()->getLanguage();
        }
        return $this->redirectToRoute('home', ['_locale' => $language]);
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

        $query = $em->createQuery(
            'SELECT i, c, u
            FROM App\Entity\Item i
            INNER JOIN i.itemCollection c
            INNER JOIN c.user u')
        ;
        $all_items = $query->getResult();
        /*var_dump($recent_items);*/
        if ($all_items) {
            $forRender['all_items'] = $all_items;
        }

        $query = $em->createQuery(
            'SELECT c, u, i
            FROM App\Entity\ItemCollection c
            INNER JOIN c.items i
            INNER JOIN c.user u')
    ;
        $collections = $query->getResult();
        /*var_dump($collections);*/
        if ($collections) {
            $forRender['full_collections'] = $collections;
        }

        return $this->render('main/index.html.twig', $forRender);
    }
}