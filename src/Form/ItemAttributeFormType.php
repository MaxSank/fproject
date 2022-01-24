<?php

namespace App\Form;

use App\Entity\ItemAttribute;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemAttributeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /*
        $types = [
            'NumberType' => NumberType::class,
            'TextType' => TextType::class,
            'TextareaType' => TextareaType::class,
            'DateType' => DateType::class,
            'CheckboxType' => CheckboxType::class,
        ];


        $builder
            ->add('value', $types[$attribute_type], array(
            'label' => "$attribute_name:",
        ))
        ;
        */
        /*var_dump($options);*/
        $builder
            ->add('value')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemAttribute::class,
        ]);
    }
}
