<?php

namespace App\Controller\Create;

use App\Controller\Main\BaseController;
use App\Entity\ItemCollectionAttribute;
use App\Form\CreateItemCollectionAttributeFormType;
use App\Repository\ItemCollectionAttributeRepository;
use App\Repository\ItemCollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CreateItemCollectionAttributeController extends BaseController
{
    private EntityManagerInterface $em;
    private TokenStorageInterface $tokenStorage;
    private ItemCollectionRepository $itemCollectionRepository;
    private ItemCollectionAttributeRepository $itemCollectionAttributeRepository;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $em,
        ItemCollectionRepository $itemCollectionRepository,
        ItemCollectionAttributeRepository $itemCollectionAttributeRepository,
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
        $this->itemCollectionRepository = $itemCollectionRepository;
        $this->itemCollectionAttributeRepository = $itemCollectionAttributeRepository;
    }

    #[Route('{_locale<%app.supported_locales%>}/user-{name}/create-attribute', name: 'create_attribute')]
    public function index($name, Request $request): Response
    {
        $forRender = parent::renderDefault();
        $forRender['controller_name'] = 'CreateItemCollectionAttributeController';
        $forRender['title'] = 'Create attribute';
        $forRender['name'] = $name;

        if (!$token = $this->tokenStorage->getToken()) {
            return $this->redirectToRoute('home');
        }

        $userRoles = $token->getUser()->getRoles();
        $userIdentifier = $token->getUser()->getUserIdentifier();

        $collection_id = (int) $request->get('collection_id');
        if ($collection_id == 0) {
            return $this->redirectToRoute('home');
        }

        $collection = $this->itemCollectionRepository->find($collection_id);
        $collection_name = $collection->getName();
        $collection_owner = $collection->getUserId()->getUserIdentifier();
        $forRender['collection_name'] = $collection_name;
        /*var_dump($collection_id, $collection, $collection_name);*/

        $number_of_attribute = count($this->itemCollectionAttributeRepository->findBy(['itemCollection' => $collection])) + 1;
        $forRender['number_of_attribute'] = $number_of_attribute;

        if (($name != $userIdentifier and !in_array('ROLE_ADMIN', $userRoles))
            or $name != $collection_owner
            or empty($collection_name)

        ) {
            return $this->redirectToRoute('home');
        }

        $attribute = new ItemCollectionAttribute();
        $form = $this->createForm(CreateItemCollectionAttributeFormType::class, $attribute);
        $forRender['createItemCollectionAttributeForm'] = $form->createView();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $attribute->setItemCollection($collection);

            $this->em->persist($attribute);
            $this->em->flush();

            return $this->redirectToRoute('create_attribute', [
                'name' => $name,
                'collection_id' => $collection_id,
            ]);
        }

        return $this->render('create_item_collection/attribute.html.twig', $forRender);
    }
}
