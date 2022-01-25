<?php

namespace App\Controller\Edit;

use App\Controller\Main\BaseController;
use App\Form\CreateItemFormType;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use DateTime;

class EditItemController extends BaseController
{
    private $em;
    private $tokenStorage;
    private $itemCollectionRepository;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $em,
        ItemCollectionRepository $itemCollectionRepository,
        ItemRepository $itemRepository,
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
        $this->itemCollectionRepository = $itemCollectionRepository;
    }


    #[Route('/{_locale<%app.supported_locales%>}/user-{name}/collection-{collection}/edit-item-{item}', name: 'edit_item')]
    public function index($name, $collection, $item, Request $request): Response
    {
        $forRender = parent::renderDefault();
        $forRender['controller_name'] = 'EditItemController';
        $forRender['title'] = 'Edit item';

        if (!$token = $this->tokenStorage->getToken()) {
            return $this->redirectToRoute('home');
        }
        $userIdentifier = $token->getUser()->getUserIdentifier();
        $collection_object = $this->itemCollectionRepository->find($collection);

        if (($name != $userIdentifier and !$this->isGranted('ROLE_ADMIN'))
            or $name != $collection_object->getUserId()->getUserIdentifier()) {
            return $this->redirectToRoute('home');
        }

        $query = $this->em->createQuery(
            'SELECT i, ia
            FROM App\Entity\Item i
            JOIN i.itemAttributes ia
            WHERE (i.id = :item_id)'
        );
        $query->setParameters([
            'item_id' => $item,

        ]);
        $full_item = $query->getSingleResult();

        $query = $this->em->createQuery(
            'SELECT ca
            FROM App\Entity\ItemCollectionAttribute ca
            WHERE (ca.itemCollection = :collection_id)'
        );
        $query->setParameters([
            'collection_id' => $collection,

        ]);
        $attributes = $query->getResult();
        $forRender['base_attributes'] = $attributes;

        /*var_dump($full_item);*/
        /*$full_item = $this->itemRepository->find($item);*/

        $form = $this->createForm(CreateItemFormType::class, $full_item);

        $forRender['editeItemForm'] = $form->createView();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $currentTime = new DateTime('now', new DateTimeZone('Europe/Minsk'));
            $full_item->setUpdatedAt($currentTime);


            $this->em->flush();

            return $this->redirectToRoute('item_collection', [
                'name' => $name,
                'id' => $collection,
            ]);
        }

        return $this->render('edit_item/index.html.twig', $forRender);
    }
}