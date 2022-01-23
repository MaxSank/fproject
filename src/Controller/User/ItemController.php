<?php

namespace App\Controller\User;

use App\Controller\Main\BaseController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemController extends BaseController
{
    private $em;

    public function __construct(
        EntityManagerInterface $em,
    ) {
        $this->em = $em;
    }

    #[Route('/{_locale<%app.supported_locales%>}/user-{name}/collection-{collection}/item-{item}', name: 'item')]
    public function index(string $name,
                          int $collection,
                          int $item,
    ): Response
    {
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Item';
        $forRender['controller_name'] = 'ItemController';

        $query = $this->em->createQuery(
            'SELECT i, c, u
            FROM App\Entity\Item i
            INNER JOIN i.itemCollection c
            INNER JOIN c.user u
            WHERE (i.id = :item_id
            AND c.id = :collection_id
            AND u.name = :user_name 
            )'
        );
        $query->setParameters([
            'item_id' => $item,
            'collection_id' => $collection,
            'user_name' => $name,
            ]);
        $full_item = $query->getSingleResult();

        if (!$full_item) {
            return $this->redirectToRoute('home');
        }

        $query = $this->em->createQuery(
            'SELECT ia, i, ca
            FROM App\Entity\ItemAttribute ia
            INNER JOIN ia.item i
            INNER JOIN ia.itemCollectionAttribute ca
            WHERE (i.id = :item_id)'
        );
        $query->setParameter(
            'item_id', $item
        );
        $attributes = $query->getResult();

        $forRender['item'] = $full_item;
        $forRender['item_id'] = $full_item->getId();
        $forRender['item_name'] = $full_item->getName();
        $collection = $full_item->getItemCollection();
        $forRender['collection_id'] = $collection->getId();
        $forRender['collection_name'] = $collection->getName();
        $owner = $collection->getUserId();
        $forRender['owner_id'] = $owner->getId();
        $forRender['owner_name'] = $owner->getUserIdentifier();

        $forRender['attributes'] = $attributes;

        return $this->render('item/index.html.twig', $forRender);
    }

}