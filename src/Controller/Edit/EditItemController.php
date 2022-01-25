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


    #[Route('/{_locale<%app.supported_locales%>}/user-{name}/collection-{collection_id}/edit-item-{item_id}', name: 'edit_item')]
    public function index($name, $collection_id, $item_id, Request $request): Response
    {
        $forRender = parent::renderDefault();
        $forRender['controller_name'] = 'EditItemController';
        $forRender['title'] = 'Edit item';

        if (!$token = $this->tokenStorage->getToken()) {
            return $this->redirectToRoute('home');
        }
        $userIdentifier = $token->getUser()->getUserIdentifier();
        $collection = $this->itemCollectionRepository->find($collection_id);

        if (($name != $userIdentifier and !$this->isGranted('ROLE_ADMIN'))
            or $name != $collection->getUserId()->getUserIdentifier()) {
            return $this->redirectToRoute('home');
        }

        $full_item = $this->itemRepository->find($item_id);

        /*$query = $this->em->createQuery(
            'SELECT i, ia
            FROM App\Entity\Item i
            JOIN i.itemAttributes ia
            WHERE (i.id = :item_id)'
        );
        $query->setParameters([
            'item_id' => $item_id,

        ]);
        $full_item = $query->getSingleResult();*/

        $query = $this->em->createQuery(
            'SELECT ca
            FROM App\Entity\ItemCollectionAttribute ca
            WHERE (ca.itemCollection = :collection_id)'
        );
        $query->setParameters([
            'collection_id' => $collection_id,

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
                'collection_id' => $collection_id,
            ]);
        }

        return $this->render('edit_item/index.html.twig', $forRender);
    }
}