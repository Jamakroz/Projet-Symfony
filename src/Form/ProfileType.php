<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo')
            ->add('prenom')
            ->add('nom')
            ->add('telephone')
            ->add('mail', TextType::class,[
                'required'=>true,
            ])
            ->add('exPassword', PasswordType::class,[
                'mapped'=>false,
                'label'=>'Ancien mot de passe',
                'required'=>true,
            ])
            ->add('site', EntityType::class,[
                'class'=>Site::class,
                'choice_label'=> 'nom',
                'attr'=>[
                    'class'=>'btn btn-secondary dropdown-toggle'
                ]
            ])
            ->add('photo', FileType::class, [
                'label' => 'Télécharger ma photo',
                'mapped' => false,
                'required' => false,
            ])
            ->add('changeProfilePicture', CheckboxType::class, [
                'label' => "Afficher ma photo de profil",
                'mapped' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
