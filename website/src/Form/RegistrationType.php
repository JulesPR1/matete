<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [ 'required' => true,'attr' => ['placeholder' => "Nom d'utilisateur", 'class' => 'form-control'] ])
            ->add('email', TextType::class, ['attr' => ['placeholder' => "Adresse email", 'class' => 'form-control mt-3']])
            ->add('numTel', TextType::class, ['attr' => ['placeholder' => "Numéro de tel", 'class' => 'form-control mt-3']])
            ->add('password', TextType::class, ['attr' => ['placeholder' => "Mot de passe", 'class' => 'form-control mt-3']])
            ->add('confirm_password', TextType::class, ['attr' => ['placeholder' => "Confirmer le mot de passe", 'class' => 'form-control mt-3']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
