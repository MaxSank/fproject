<?php

namespace App\Controller\Create;

use App\Controller\Main\BaseController;
use App\Entity\Item;
use App\Entity\ItemCollection;
use App\Form\CreateItemCollectionFormType;
use App\Form\CreateItemFormType;
use App\Repository\ItemCollectionRepository;
use App\Repository\UserRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use DateTime;

class CreateItemController extends BaseController
{
    private $em;
    private $tokenStorage;
    private $userRepository;
    private $itemCollectionRepository;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        ItemCollectionRepository $itemCollectionRepository,
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->itemCollectionRepository = $itemCollectionRepository;
    }


    #[Route('/{_locale<%app.supported_locales%>}/user-{name}/collection-{id}/create-item', name: 'create_item')]
    public function index($name, $id, Request $request): Response
    {
        $forRender = parent::renderDefault();
        $forRender['controller_name'] = 'CreateItemController';
        $forRender['title'] = 'Create item';

        if (!$token = $this->tokenStorage->getToken()) {
            return $this->redirectToRoute('home');
        }
        $userRoles = $token->getUser()->getRoles();
        $userIdentifier = $token->getUser()->getUserIdentifier();
        $collection = $this->itemCollectionRepository->find($id);


        if (($name != $userIdentifier and !in_array('ROLE_ADMIN', $userRoles))
            or $name != $collection->getUserId()->getUserIdentifier()) {
            return $this->redirectToRoute('home');
        }


        $item = new Item();
        $form = $this->createForm(CreateItemFormType::class, $item);
        $forRender['createItemForm'] = $form->createView();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $item->setItemCollection($collection);

            $currentTime = new DateTime('now', new DateTimeZone('Europe/Minsk'));
            $item->setCreatedAt($currentTime);
            $item->setUpdatedAt($currentTime);

            $this->em->persist($item);
            $this->em->flush();

            return $this->redirectToRoute('item_collection', [
                'name' => $name,
                'id' => $collection->getId(),
            ]);
        }

        return $this->render('create_item/index.html.twig', $forRender);
    }
}
