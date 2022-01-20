<?php

namespace App\Controller\User;

use App\Controller\Main\BaseController;
use App\Repository\ItemCollectionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PersonalPageController extends BaseController
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

    #[Route('/{_locale<%app.supported_locales%>}/user-{name}', name: 'user')]
    public function index(string $name, ItemCollectionRepository $itemCollectionRepository, UserRepository $userRepository)
    {
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Personal page';

        $user = $userRepository->findOneBy(array('name' => $name));
        if (!$user) {
            return $this->redirectToRoute('home');
        }
        $userId = $user->getId();

        $collections = $itemCollectionRepository->findBy(array('user' => $userId), array('name' => 'ASC'));
        $forRender['collections'] = $collections;
        $forRender['owner_id'] = $userId;
        $forRender['owner_name'] = $name;

        return $this->render('personalpage/index.html.twig', $forRender);

    }

    #[Route('/{language}/user-{name}', name: 'userAuthentication')]
    public function indexAuth(string $language, string $name)
    {
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Personal page';
        return $this->render('personalpage/index.html.twig', $forRender);

    }

    #[Route("/{_locale<%app.supported_locales%>}/delete-collection-{id}", name: "delete_item_collection", methods: "get")]
    public function deleteItemCollection($id, ItemCollectionRepository $itemCollectionRepository)
    {
        $itemCollection = $itemCollectionRepository->find($id);
        $token = $this->tokenStorage->getToken();
        if (!$token or !$itemCollection) {
            return $this->redirectToRoute('home');
        }

        $userId = $token->getUser()->getId();
        $userRoles = $token->getUser()->getRoles();
        if ($userId != $itemCollection->getUserId()->getId() and !in_array('ROLE_ADMIN', $userRoles)) {
            return $this->redirectToRoute('home');
        }

        $this->em->remove($itemCollection);
        $this->em->flush();


        return $this->redirectToRoute('user', [
            'name' => $itemCollection->getUserId()->getUserIdentifier(),
        ]);
    }

}