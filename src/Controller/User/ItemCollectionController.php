<?php

namespace App\Controller\User;

use App\Controller\Main\BaseController;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemCollectionController extends BaseController
{

    #[Route('/{_locale<%app.supported_locales%>}/user-{name}/collection-{id}', name: 'item_collection')]
    public function index(string $name,
                          int $id,
                          ItemCollectionRepository $itemCollectionRepository,
                          UserRepository $userRepository,
                          ItemRepository $itemRepository,
                        ): Response
    {
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Collection';
        $forRender['controller_name'] = 'ItemCollectionController';

        $itemCollection = $itemCollectionRepository->find($id);
        $user = $userRepository->findOneBy(array('name' => $name));
        if (!$user or !$itemCollection) {
            return $this->redirectToRoute('home');
        }
        $collectionId = $itemCollection->getId();
        $items = $itemRepository->findBy(array('itemCollection' => $collectionId), array('createdAt' => 'ASC'));

        $forRender['collection_name'] = $itemCollection->getName();
        $forRender['collection_id'] = $collectionId;
        $forRender['owner_name'] = $user->getUserIdentifier();
        $forRender['owner_id'] = $user->getId();
        $forRender['items'] = $items;

        return $this->render('item_collection/index.html.twig', $forRender);
    }



}


