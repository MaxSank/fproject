<?php

namespace App\Controller\Create;

use App\Controller\Main\BaseController;
use App\Entity\ItemCollection;
use App\Form\CreateItemCollectionFormType;
use App\Repository\UserRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class CreateItemCollectionController extends BaseController
{

    private $em;
    private $tokenStorage;
    private $userRepository;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $em,
        UserRepository $userRepository,
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    #[Route('/{_locale<%app.supported_locales%>}/user-{name}/create-collection', name: 'create_item_collection')]
    public function index($name, Request $request): Response
    {
        $forRender = parent::renderDefault();
        $forRender['controller_name'] = 'CreateItemCollectionController';
        $forRender['title'] = 'Create collection';

        $token = $this->tokenStorage->getToken();

        if ($token != null) {

            $userRoles = $token->getUser()->getRoles();
            $userIdentifier = $token->getUser()->getUserIdentifier();

            if ($name == $userIdentifier or in_array('ROLE_ADMIN', $userRoles)) {
                $collection = new ItemCollection();
                $form = $this->createForm(CreateItemCollectionFormType::class, $collection);
                $forRender['createItemCollectionForm'] = $form->createView();
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                    $user = $this->userRepository->findOneBy(array('name' => $name));
                    $collection->setUserId($user);

                    $currentTime = new DateTime('now', new DateTimeZone('Europe/Minsk'));
                    $collection->setCreatedAt($currentTime);

                    $this->em->persist($collection);
                    $this->em->flush();

                    return $this->redirectToRoute('create_attribute', [
                        'name' => $name,
                        'collection_id' => $collection->getId(),
                    ]);
                }
            } else {
                return $this->redirectToRoute('home');
            }
        } else {
            return $this->redirectToRoute('home');
        }


        return $this->render('create_item_collection/index.html.twig', $forRender);
    }

}
