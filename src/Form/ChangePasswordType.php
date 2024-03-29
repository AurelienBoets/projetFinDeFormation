<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                "disabled" => true,
                "label" => "Email"
            ])
            ->add('firstName', TextType::class, [
                "disabled" => true,
                "label" => "Prénom"
            ])
            ->add('lastName', TextType::class, [
                "disabled" => true,
                "label" => "Nom"
            ])
            ->add('oldPassword', PasswordType::class, [
                "label" => "Mot de passe actuel",
                "mapped" => false,
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                "mapped" => false,
                'invalid_message' => "Le mot de passe et la confirmation doivent être identiques",
                'required' => true,
                "first_options" => [
                    'label' => 'Nouveau mot de passe',
                ],
                "second_options" => [
                'label' => "Confirmation du nouveau mot de passe"
                ]

            ])
            ->add('submit', SubmitType::class, [
                'label' => "Modifier"
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
