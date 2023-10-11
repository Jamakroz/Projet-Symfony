<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('motifAnnulation')
            ->add('photoSortie')
            //TODO: retirer ETAT quand sera gérer dans le back
            ->add('etat')
            ->add('lieu')
            ->add('site', EntityType::class,[
                'class'=>Site::class,
                'choice_label'=> 'nom',
                'attr'=>[
                    'class'=>'btn btn-secondary dropdown-toggle'
                ]
            ])
//            ->add('organisateur')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
