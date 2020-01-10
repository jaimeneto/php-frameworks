<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostForm extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(
    FormBuilderInterface $builder,
    array $options
  ) {
    $builder
      ->add('title', TextType::class, [
        'constraints' => [new NotBlank()],
        'attr'        => ['class' =>
        'form-control']
      ])
      ->add('text', TextareaType::class, [
        'constraints' => [new NotBlank()],
        'attr'        => [
          'class' => 'form-control',
          'rows'  => 10
        ]
      ])
      ->add('submit', SubmitType::class, [
        'attr'  => [
          'class' => 'form-control btn-primary'
        ],
        'label' => 'Save'
      ]);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(
    OptionsResolver $resolver
  ) {
    $resolver->setDefaults([
      'data_class' => 'App\Entity\Post'
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'post_form';
  }
}
