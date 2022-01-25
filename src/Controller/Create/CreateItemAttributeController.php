<?php

namespace App\Controller\Create;

use App\Controller\Main\BaseController;
use App\Entity\ItemAttribute;
use App\Form\ItemAttributeFormType;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CreateItemAttributeController extends BaseController
{
    private $em;
    private $tokenStorage;
    private $itemCollectionRepository;
    private $itemRepository;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $em,
        ItemCollectionRepository $itemCollectionRepository,
        ItemRepository $itemRepository,
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
        $this->itemCollectionRepository = $itemCollectionRepository;
        $this->itemRepository = $itemRepository;
    }


    #[Route('{_locale<%app.supported_locales%>}/user-{name}/collection-{collection_id}/item-{item_id}/set-attribute-value', name: 'item_attribute_value')]
    public function index($name, $collection_id, $item_id, Request $request): Response
    {
        $forRender = parent::renderDefault();
        $forRender['controller_name'] = 'CreateItemAttributeController';
        $forRender['title'] = 'Set value of attribute';

        if (!$token = $this->tokenStorage->getToken()) {
            return $this->redirectToRoute('home');
        }
        $userIdentifier = $token->getUser()->getUserIdentifier();
        $collection = $this->itemCollectionRepository->find($collection_id);
        $item = $this->itemRepository->find($item_id);
        $forRender['item_name'] = $item->getName();

        if (($name != $userIdentifier and !$this->isGranted('ROLE_ADMIN'))
            or $name != $collection->getUserId()->getUserIdentifier()) {
            return $this->redirectToRoute('home');
        }

        if (($item->getItemCollection()->getId() != $collection_id)) {
            return $this->redirectToRoute('home');
        }

        $query = $this->em->createQuery(
            'SELECT ca
            FROM App\Entity\ItemCollectionAttribute ca
            WHERE (ca.itemCollection = :collection_id)'
        );
        $query->setParameters([
            'collection_id' => $collection_id,

        ]);
        $attributes = $query->getResult();
        if (!$attributes) {
            return $this->redirectToRoute('item', [
                'name' => $name,
                'collection_id' => $collection_id,
                'item_id' => $item_id,
            ]);
        }


        $forRender['base_attributes'] = $attributes;

        $query = $this->em->createQuery(
            'SELECT ia
            FROM App\Entity\ItemAttribute ia
            WHERE (ia.item = :item_id)'
        );
        $query->setParameters([
            'item_id' => $item_id,

        ]);
        $existed_attributes = $query->getResult();

        $existed_attributes_count = count($existed_attributes);
        $number_of_attribute = $existed_attributes_count + 1;
        $forRender['number_of_attribute'] = $number_of_attribute;
        $item_collection_attribute = $attributes[$existed_attributes_count];
        $forRender['item_collection_attribute'] = $item_collection_attribute;

        $itemAttribute = new ItemAttribute();
        $form = $this->createForm(ItemAttributeFormType::class, $itemAttribute);
        $forRender['createItemAttributeForm'] = $form->createView();
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $itemAttribute->setItem($item);
            $itemAttribute->setItemCollectionAttribute($item_collection_attribute);

            $this->em->persist($itemAttribute);
            $this->em->flush();

            if (count($attributes) == $number_of_attribute) {
                return $this->redirectToRoute('item', [
                    'name' => $name,
                    'collection_id' => $collection_id,
                    'item_id' => $item_id,
                ]);

            } else {
                return $this->redirectToRoute('item_attribute_value', [
                    'name' => $name,
                    'collection_id' => $collection_id,
                    'item_id' => $item_id,
                ]);
            }

        }

        return $this->render('create_item/attribute.html.twig', $forRender);

    }
}