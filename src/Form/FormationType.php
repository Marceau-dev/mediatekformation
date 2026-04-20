<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le titre est obligatoire.',
                    ]),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Le titre ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('videoId', TextType::class, [
                'label' => 'Identifiant vidéo',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'L’identifiant vidéo est obligatoire.',
                    ]),
                    new Length([
                        'max' => 20,
                        'maxMessage' => 'L’identifiant vidéo ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('publishedAt', DateType::class, [
                'label' => 'Date de publication',
                'widget' => 'single_text',
                'required' => true,
                'data' => isset($options['data']) && $options['data']->getPublishedAt() !== null
                    ? $options['data']->getPublishedAt()
                    : new DateTime('now'),
                'attr' => [
                    'max' => (new DateTime('now'))->format('Y-m-d'),
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La date est obligatoire.',
                    ]),
                    new LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date ne doit pas être postérieure à la date du jour.',
                    ]),
                ],
            ])
            ->add('playlist', EntityType::class, [
                'label' => 'Playlist',
                'class' => Playlist::class,
                'choice_label' => 'name',
                'required' => true,
                'placeholder' => 'Sélectionner une playlist',
                'constraints' => [
                    new NotBlank([
                        'message' => 'La playlist est obligatoire.',
                    ]),
                ],
            ])
            ->add('categories', EntityType::class, [
                'label' => 'Catégories',
                'class' => Categorie::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}