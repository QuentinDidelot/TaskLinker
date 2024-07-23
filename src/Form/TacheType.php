<?php

namespace App\Form;

use App\Entity\Employe;
use App\Entity\Statut;
use App\Entity\Tache;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class TacheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de la tâche',
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'Description',
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ])
            ->add('deadline', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date',
            ])
            ->add('statut', EntityType::class, [
                'class' => Statut::class,
                'choice_label' => 'libelle',
                'label' => 'Statut',
            ])
            ->add('employe', EntityType::class, [
                'class' => Employe::class,
                'choice_label' => function (Employe $employe) {
                    return $employe->getPrenom() . ' ' . $employe->getNom();
                },
                'required' => false,
                'label' => 'Membre',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('t')
                        ->leftJoin('t.projets' , 'p')
                        ->where('p.id = :projet_id')
                        ->setParameter(':projet_id', $options['projet_id'] )
                        ->orderBy('t.nom', 'ASC');
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
            'projet_id' => null,
        ]);
    }
}
