<?php

namespace App\Controller\Edit;

use App\Controller\Main\BaseController;
use App\Form\CreateItemCollectionFormType;
use App\Repository\ItemCollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EditItemCollectionController extends BaseController
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

    #[Route('/{_locale<%app.supported_locales%>}/user-{name}/edit-collection-{collection_id}', name: 'edit_item_collection')]
    public function index($name, $collection_id, Request $request, ItemCollectionRepository $itemCollectionRepository): Response
    {
        $forRender = parent::renderDefault();
        $forRender['controller_name'] = 'EditItemCollectionController';
        $forRender['title'] = 'Edit collection';

        if (!$token = $this->tokenStorage->getToken()) {
            return $this->redirectToRoute('home');
        }
        $userIdentifier = $token->getUser()->getUserIdentifier();

        if ($name != $userIdentifier and !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home');
        }

        $collection = $itemCollectionRepository->find($collection_id);
        $form = $this->createForm(CreateItemCollectionFormType::class, $collection);
        $forRender['editItemCollectionForm'] = $form->createView();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->flush();

            return $this->redirectToRoute('user', [
                'name' => $name,
            ]);
        }

        return $this->render('edit_item_collection/index.html.twig', $forRender);
    }
}
