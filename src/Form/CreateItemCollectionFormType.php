<?php

namespace App\Form;

use App\Entity\ItemCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CreateItemCollectionFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name', TextType::class, array(
                'translation_domain' => 'messages',
                'label' => 'Enter a name for the collection:',
            ))
            ->add('description', TextareaType::class,  array(
                'translation_domain' => 'messages',
                'label' => 'Enter a description for the collection:',
                'attr' => array('rows' => '8'),
            ))
            ->add('theme', ChoiceType::class, array(
                'choices'  => [
                    'Alcohol' => 'Alcohol',
                    'Antiques' => 'Antiques',
                    'Art' => 'Art',
                    'Models' => 'Models',
                    'Money' => 'Money',
                    'Non-artificial objects' => 'Non-artificial objects',
                    'Printed products' => 'Printed products',
                    'Souvenirs' => 'Souvenirs',
                    'Stamps' => 'Stamps',
                    'Weapons and military' => 'Weapons and military',
                    'Other' => 'Other',
                ],
                'translation_domain' => 'messages',
                'label' => 'Select a theme for the collection:',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemCollection::class,
        ]);
    }
}
