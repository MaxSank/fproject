<?php

namespace App\Controller\Create;

use App\Entity\ItemCollection;
use App\Form\ItemCollectionCreateFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CreateItemCollectionController extends AbstractController
{

    private $em;
    private $tokenStorage;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $em,
        UserRepository $userRepository,
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    #[Route('/{_locale<%app.supported_locales%>}/create-collection', name: 'create_item_collection')]
    public function index(Request $request): Response
    {
        $collection = new ItemCollection();
        $form = $this->createForm(ItemCollectionCreateFormType::class, $collection);
        $form->handleRequest($request);

        $token = $this->tokenStorage->getToken();
        $user_from_token = $token->getUser();
        $user_id = $user_from_token->getId();

        $user = $this->userRepository->find($user_id);

        if ($form->isSubmitted() && $form->isValid()) {
            $collection->setUserId($user);

            $this->em->persist($collection);
            $this->em->flush();

            return $this->redirectToRoute('user', [
                'name' => $token->getUserIdentifier(),
                ]);
        }

        return $this->render('create_item_collection/index.html.twig', [
            'controller_name' => 'CreateItemCollectionController',
            'createItemCollectionForm' => $form->createView(),
            'title' => 'Create collection',
        ]);
    }
}
