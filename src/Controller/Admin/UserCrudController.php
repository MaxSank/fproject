<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            EmailField::new('email'),
            ChoiceField::new('hasRoleAdmin', 'Admin')
                ->setChoices([
                    'No' => 'No',
                    'Yes' => 'Yes',
                ]),
            DateTimeField::new('registrationDate')
                ->setSortable(true)
                ->setFormat('Y-MM-dd HH:mm')
                ->renderAsText(),
            DateTimeField::new('lastLoginDate')
                ->setSortable(true)
                ->setFormat('Y-MM-dd HH:mm')
                ->renderAsText(),
            ChoiceField::new('status')->setChoices([
                'Not blocked' => 'Not blocked',
                'Blocked' => 'Blocked'
            ]),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ;
    }


}
