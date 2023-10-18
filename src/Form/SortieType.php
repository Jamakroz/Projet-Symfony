<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut')
            ->add('duree')
            ->add('dateLimiteInscription')
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'attr'=>[
                    'class'=>'btn btn-secondary dropdown-toggle',
                ]
            ])
            ->add('ville', EntityType::class,[
                'mapped'=>false,
                'class'=>Ville::class,
                'choice_label'=> 'nom',
                'attr'=>[
                    'class'=>'btn btn-secondary dropdown-toggle form-control',
                ]
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'État de la Sortie',
                'choices' => [
                    'En préparation' => 'en_preparation',
                    'En cours' => 'en_cours',
                    'Terminée' => 'terminee',
                    // Ajoutez d'autres états si nécessaire
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
