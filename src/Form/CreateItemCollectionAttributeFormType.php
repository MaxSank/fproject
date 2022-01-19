<?php

namespace App\Form;

use App\Entity\ItemCollectionAttribute;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateItemCollectionAttributeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, array(
                'translation_domain' => 'messages',
                'label' => 'Enter a name for the attribute:',
            ))
            ->add('attributeType', ChoiceType::class, array(
                'choices'  => [
                    'Number' => 'NumberType',
                    'Text' => 'TextType',
                    'Text area' => 'TextareaType',
                    'Date' => 'DateType',
                    'Checkbox' => 'CheckboxType',
                ],
                'translation_domain' => 'messages',
                'label' => 'Select a type for the attribute:',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemCollectionAttribute::class,
        ]);
    }
}
