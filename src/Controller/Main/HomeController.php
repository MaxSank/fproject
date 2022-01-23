<?php

namespace App\Controller\Main;

use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManager;
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
        ItemCollectionRepository $itemCollectionRepository,
        EntityManagerInterface $em,
    )
    {
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Welcome!';

        $recent_items = $itemRepository->findBy([], ['createdAt' => 'DESC'], 5);
        if ($recent_items) {
            $forRender['recent_items'] = $recent_items;
        }

        $query = $em->createQuery('SELECT IDENTITY(i.itemCollection) FROM App\Entity\Item i');
        $ids_array = $query->getResult();
        if ($ids_array) {
            $ids = array();
            array_walk_recursive($ids_array, function($item, $key) use (&$ids){
                $ids[] = $item;
            });
            $ids = array_count_values($ids);
            arsort($ids);

            $collections = array_keys($ids);
            $query = $em->createQuery(
                'SELECT c, u, i
                FROM App\Entity\ItemCollection c
                INNER JOIN c.items i
                INNER JOIN c.user u
                WHERE
                c.id IN (:collection_ids)')
                ->setParameters(['collection_ids' => $collections]);

            $biggest_collections = $query->getResult();


            $forRender['biggest_collections'] = $biggest_collections;
        }

        return $this->render('main/index.html.twig', $forRender);
    }
}