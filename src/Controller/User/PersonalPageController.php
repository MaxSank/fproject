<?php

namespace App\Controller\User;

use App\Controller\Main\BaseController;
use App\Repository\ItemCollectionRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PersonalPageController extends BaseController
{
    #[Route('/user-{name}')]
    public function indexNoLocaleUser($name, TokenStorageInterface $tokenStorage): Response
    {
        if (!$token = $tokenStorage->getToken()) {
            $language = 'en';
        } else {
            $language = $token->getUser()->getLanguage();
        }
        return $this->redirectToRoute('user', ['name' => $name, '_locale' => $language]);
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
}