<?php

namespace App\Controller\Create;

use App\Controller\Main\BaseController;
use App\Entity\ItemCollection;
use App\Form\CreateItemCollectionFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class CreateItemCollectionController extends BaseController
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
        $token = $this->tokenStorage->getToken();
        $forRender = parent::renderDefault();
        $forRender['controller_name'] = 'CreateItemCollectionController';
        $forRender['title'] = 'Create collection';

        if ($token != null) {
            $collection = new ItemCollection();
            $form = $this->createForm(CreateItemCollectionFormType::class, $collection);
            $forRender['createItemCollectionForm'] = $form->createView();
            $form->handleRequest($request);

            $user_from_token = $token->getUser();
            $user_id = $user_from_token->getId();

            $user = $this->userRepository->find($user_id);
            $collection->setUserId($user);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->em->persist($collection);
                $this->em->flush();

                return $this->redirectToRoute('user', [
                    'name' => $token->getUserIdentifier(),
                ]);
            }
        }


        return $this->render('create_item_collection/index.html.twig', $forRender);
    }
}
