<?php

namespace App\Form;

use App\Classe\Search;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('string', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Votre recherche',
                    'class' => 'searchProduct'
                ]
                ])
            ->add('submit', SubmitType::class, [
                'label' => ' ',
                'attr' => [
                    'class' => 'submitProduct'
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET',
            "crsf_protection" => false, //Désactive le cryptage de l'url
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
