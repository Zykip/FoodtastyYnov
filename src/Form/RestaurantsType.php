<?php

namespace App\Form;

use App\Entity\Restaurants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestaurantsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Restaurant Name',
                'attr' => [
                    'autofocus' => 'autofocus'
                ]
            ])
            ->add('email')
            ->add('address', null, [
                'label' => 'Restaurant Address'
            ])
            ->add('mediaId', FileType::class, [
                'mapped' => false,
                'label' => 'Photo'
            ])
//            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Restaurants::class,
            'attr' => [
                'class' => 'js-form'
            ]
        ]);
    }
}
