<?php

namespace App\Controller\User;

use App\Controller\Main\BaseController;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ItemCollectionController extends BaseController
{
    private $em;
    private $tokenStorage;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $em,
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
    }

    #[Route('/{_locale<%app.supported_locales%>}/user-{name}/collection-{collection_id}', name: 'item_collection')]
    public function index(string $name,
                          int $collection_id,
                          ItemCollectionRepository $itemCollectionRepository,
                          UserRepository $userRepository,
                          ItemRepository $itemRepository,
                        ): Response
    {
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Collection';
        $forRender['controller_name'] = 'ItemCollectionController';

        $itemCollection = $itemCollectionRepository->find($collection_id);
        $user = $userRepository->findOneBy(array('name' => $name));
        if (!$user or !$itemCollection) {
            return $this->redirectToRoute('home');
        }

        /*$items = $itemRepository->findBy(array('itemCollection' => $collectionId), array('createdAt' => 'ASC'));*/
        $check_query = $this->em->createQuery(
            'SELECT ca
            FROM App\Entity\ItemCollectionAttribute ca
            WHERE (ca.itemCollection = :collection_id)'
        );
        $check_query->setParameter('collection_id', $collection_id);
        $attributes = $check_query->getResult();

        if ($attributes) {
            $query = $this->em->createQuery(
            'SELECT i, c, ca, ia
            FROM App\Entity\Item i
            INNER JOIN i.itemCollection c
            INNER JOIN c.itemCollectionAttributes ca
            INNER JOIN i.itemAttributes ia
            WHERE (c.id = :collection_id)'
            );
            $query->setParameter('collection_id', $collection_id);
            $items = $query->getResult();
        } else {
            $query = $this->em->createQuery(
                'SELECT i, c
            FROM App\Entity\Item i
            INNER JOIN i.itemCollection c
            WHERE (c.id = :collection_id)'
            );
            $query->setParameter('collection_id', $collection_id);
            $items = $query->getResult();
        }

        /*$itemCollectionAttributes = $itemCollectionRepository->findOneBy(['itemCollection' => $collectionId])->getItemCollectionAttributes();*/

        $forRender['collection_name'] = $itemCollection->getName();
        $forRender['collection_id'] = $collection_id;
        $forRender['owner_name'] = $user->getUserIdentifier();
        $forRender['owner_id'] = $user->getId();
        $forRender['items'] = $items;
        /*$forRender['attributes'] = $itemCollectionAttributes;*/

        return $this->render('item_collection/index.html.twig', $forRender);
    }

    #[Route("/{_locale<%app.supported_locales%>}/delete-collection-{collection_id}", name: "delete_item_collection", methods: "get")]
    public function deleteItemCollection($collection_id, ItemCollectionRepository $itemCollectionRepository)
    {
        $itemCollection = $itemCollectionRepository->find($collection_id);
        $token = $this->tokenStorage->getToken();
        if (!$token or !$itemCollection) {
            return $this->redirectToRoute('home');
        }

        $userId = $token->getUser()->getId();
        if ($userId != $itemCollection->getUserId()->getId() and !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home');
        }

        $this->em->remove($itemCollection);
        $this->em->flush();


        return $this->redirectToRoute('user', [
            'name' => $itemCollection->getUserId()->getUserIdentifier(),
        ]);
    }

}


