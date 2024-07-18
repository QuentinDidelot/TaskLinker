<?php

namespace App\Form;

use App\Entity\Projet;
use App\Entity\Employe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Titre du projet',
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ])
            ->add('employe', EntityType::class, [
                'class' => Employe::class,
                'label' => 'Inviter des membres',
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => false,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
        ]);
    }
}
