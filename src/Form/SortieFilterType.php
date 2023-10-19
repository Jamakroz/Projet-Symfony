<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Sortie;
use App\Enum\Etat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('site', EntityType::class,[
                'class'=>Site::class,
                'choice_label'=> 'nom',
                'attr'=>[
                    'class'=>'btn btn-secondary dropdown-toggle'
                ]
            ])
            ->add('nom', TextType::class, [
                'mapped'=>false,
                'required'=>false
            ])
            ->add('etat', ChoiceType::class, [
                'choices' => Etat::toArray(), // ceci génère un tableau à partir de vos constantes de type énumérées
            ])
            ->add('dateDebut', DateTimeType::class, [
                'mapped'=>false,
                'widget' => 'single_text',
                'html5' => true,
                'required'=>false
            ])
            ->add('dateFin', DateTimeType::class, [
                'mapped'=>false,
                'widget' => 'single_text',
                'html5' => true,
                'data'=>null,
                'required'=>false
            ])
//            ->add('isOrganisateur')
//            ->add('isInscrit')
//            ->add('isNotInscrit')
//            ->add('isPastSortie')
            ->add('mesChoix', ChoiceType::class, [
                'choices'  => [
                    'Je susi organisateur' => 'isOrganisateur',
                    'Je suis inscrit' => 'isInscrit',
                    'Je suis pas inscrit' => 'isNotInscrit'
                ],
                'expanded' => true,
                'multiple' => true,
                'mapped'=>false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
//            'data_class' => Sortie::class,
        ]);
    }
}
