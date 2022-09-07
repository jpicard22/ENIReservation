<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use \Symfony\Component\Form\Extension\Core\Type\TextType;

class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie'
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'label' => 'Date limite d\'inscription',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre de places'
            ])
            ->add('duree', IntegerType::class, [
                'label' => "Durée (en minutes)"
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos'
            ])
            ->add('siteOrganisateur', EntityType::class, [
                'label' => 'Campus',
                'class' => Campus::class,
                'query_builder'=> function (CampusRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.nom', 'ASC');
                },
                'choice_label' => 'nom',
                'choice_value' => 'id',
                'placeholder' => 'Choisir un Campus'
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'query_builder'=> function (LieuRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.nom', 'ASC');
                },
                'choice_label' => 'nom',
                'choice_value' => 'id',
                'placeholder' => 'Choisir un lieu'
            ])
            ->add("ville", TextType::class, [
                'label' => "Ville",
                'mapped' => false,
                'attr' => [
                    'disabled' => true
                ]
            ])
            ->add("codePostal", TextType::class, [
                'label' => "Code Postal",
                'mapped' => false,
                'attr' => [
                    'disabled' => true
                ]
            ])
            ->add("rue", TextType::class, [
                'label' => "Rue",
                'mapped' => false,
                'attr' => [
                    'disabled' => true
                ]
            ])
            ->add("latitude", TextType::class, [
                'label' => "Latitude",
                'mapped' => false,
                'attr' => [
                    'disabled' => true
                ]
            ])
            ->add("longitude", TextType::class, [
                'label' => "Longitude",
                'mapped' => false,
                'attr' => [
                    'disabled' => true
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
