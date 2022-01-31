<?php

namespace App\Controller\Search;

use App\Controller\Main\BaseController;
use App\Entity\Item;
use App\Entity\ItemAttribute;
use App\Entity\ItemCollection;
use App\Entity\ItemCollectionAttribute;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\Query\ResultSetMapping;


class SearchController extends BaseController
{
    private $em;

    public function __construct(
        EntityManagerInterface $em,
    ) {
        $this->em = $em;
    }


    #[Route('/{_locale<%app.supported_locales%>}/search', name: 'search')]
    public function showItems(Request $request) {
        $forRender['title'] = 'Search results';
        $forRender['search_result'] = [];
        if ($request->request->has('form')
            and array_key_exists('search', (array) $request->request->get('form'))) {
            $search_request = $request->request->get('form')['search'];

            $items = $this->searchItems($search_request);

            $forRender['search_keyword'] = $search_request;
            $forRender['search_result'] = $items;

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


    public function searchItems($search_request)
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Item::class, 'i');

        $rsm->addFieldResult('i', 'id', 'id');
        $rsm->addFieldResult('i', 'item_name', 'name');

        $rsm->addJoinedEntityResult(ItemCollection::class,'c', 'i', 'itemCollection');
        $rsm->addFieldResult('c', 'col_id', 'id');
        $rsm->addFieldResult('c', 'col_name', 'name');
        $rsm->addFieldResult('c', 'col_desc', 'description');

        $rsm->addJoinedEntityResult(User::class,'u', 'c', 'user');
        $rsm->addFieldResult('u', 'user_id', 'id');
        $rsm->addFieldResult('u', 'user_name', 'name');

        $rsm->addJoinedEntityResult(ItemCollectionAttribute::class,'ic', 'c', 'itemCollectionAttributes');
        $rsm->addFieldResult('ic', 'col_attr_id', 'id');
        $rsm->addFieldResult('ic', 'col_attr_name', 'name');

        $rsm->addJoinedEntityResult(ItemAttribute::class,'ia', 'i', 'itemAttributes');
        $rsm->addFieldResult('ia', 'attr_id', 'id');
        $rsm->addFieldResult('ia', 'attr_value', 'value');


        $sql = 'SELECT 
                i.id,
                i.name as item_name,
                c.id as col_id,
                c.name as col_name,
                c.description as col_desc,
                u.id as user_id,
                u.name as user_name,
                ic.id as col_attr_id,
                ic.name as col_attr_name,
                ia.id as attr_id,
                ia.value as attr_value
                FROM item AS i
                INNER JOIN  item_collection c ON i.item_collection_id = c.id
                INNER JOIN user u ON c.user_id = u.id
                INNER JOIN item_collection_attribute ic ON c.id = ic.item_collection_id
                INNER JOIN item_attribute ia ON i.id = ia.item_id
                
                
                WHERE 
                MATCH (i.name) AGAINST (? IN NATURAL LANGUAGE MODE)
                OR
                MATCH (c.name, c.description) AGAINST (? IN NATURAL LANGUAGE MODE)
                OR
                MATCH (u.name) AGAINST (? IN NATURAL LANGUAGE MODE)
                OR
                MATCH (ic.name) AGAINST (? IN NATURAL LANGUAGE MODE)
                OR
                MATCH (ia.value) AGAINST (? IN NATURAL LANGUAGE MODE)';

        $query = $this->em->createNativeQuery($sql, $rsm);
        $query->setParameters([
            1 => $search_request,
            2 => $search_request,
            3 => $search_request,
            4 => $search_request,
            5 => $search_request,
        ]);

        $items = $query->getResult();

        return $items;

    }

}