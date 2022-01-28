<?php

namespace App\Controller\Search;

use App\Controller\Main\BaseController;
use App\Repository\ItemCollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Elastica\Util;

class SearchController extends BaseController
{
    private $em;

    public function __construct(
        EntityManagerInterface $em,
    ) {
        $this->em = $em;
    }

    #[Route('/{_locale<%app.supported_locales%>}/search', name: 'search')]
    public function showItems(Request $request, TransformedFinder $itemFinder) {
        $forRender['title'] = 'Search results';
        $forRender['search_result'] = [];
        if ($request->request->has('form')
            and array_key_exists('search', (array) $request->request->get('form'))) {
            $search_request = $request->request->get('form')['search'];
            $search = Util::escapeTerm($search_request);
            $forRender['search_keyword'] = $search_request;
            $forRender['search_result'] = $itemFinder->find($search);

        }
        return $this->render('search/items.html.twig', $forRender);
    }

    public function searchBar(): Response
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('search'))
            ->add('search')
            ->getForm();

        return $this->render('search/bar.html.twig', [
            'searchForm' => $form->createView()
        ]);
    }

}