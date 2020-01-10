<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentForm extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(
    FormBuilderInterface $builder,
    array $options
  ) {
    $builder
      ->add('text', TextareaType::class, [
        // Não exibe o label
        'label'       => false,
        'constraints' => [new NotBlank()],
        'attr'        => [
          'class'       => 'form-control',
          'rows'        => 2,
          'placeholder' =>
          'Insert your comments here'
        ]
      ])
      ->add('submit', SubmitType::class, [
        'attr'  => [
          'class' => 'form-control btn-primary'
        ],
        'label' => 'Save'
      ])
      ->add('reset', ResetType::class, [
        'attr'  => ['class' =>
        'form-control btn-outline-secondary'],
        'label' => 'Reset'
      ]);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(
    OptionsResolver $resolver
  ) {
    $resolver->setDefaults([
      'data_class' => 'App\Entity\Comment'
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'comment_form';
  }
}
