<?php

namespace App\Controller\User;

use App\Controller\Main\BaseController;
use App\Repository\ItemCollectionRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class PersonalPageController extends BaseController
{
    #[Route('/{_locale<%app.supported_locales%>}/user-{name}', name: 'user')]
    public function index(string $name, UserInterface $user, ItemCollectionRepository $itemCollectionRepository)
    {
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Personal page';

        $userId = $user->getId();
        $collections = $itemCollectionRepository->findBy(array('user' => $userId), array('name' => 'ASC'));
        $forRender['collections'] = $collections;

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