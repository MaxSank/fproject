<?php

namespace App\Controller\Main;

use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
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
        ItemCollectionRepository $itemCollectionRepository)
    {
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Welcome!';

        $recent_items = $itemRepository->findBy([], ['createdAt' => 'DESC'], 5);
        if ($recent_items){
            $forRender['recent_items'] = $recent_items;
        }

        /*$forRender['biggest_collections'] = $itemCollectionRepository->findAll();*/
        $collections = $itemCollectionRepository->findAll();
        if ($collections) {
            $collections_size = [];
            foreach ($collections as $single_collection) {
                $size = count($single_collection->getItems());

                $collections_size[(string) $single_collection->getId()] =  $size;
            }
            arsort($collections_size);
            $collections_size = array_keys($collections_size);

            $biggest_collections = [];
            foreach ($collections_size as $single_collection) {
                $biggest_collections[] = $itemCollectionRepository->find($single_collection);
            }
            $forRender['biggest_collections'] = $biggest_collections;
        }

        return $this->render('main/index.html.twig', $forRender);
    }
}