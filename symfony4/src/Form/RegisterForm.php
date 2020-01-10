<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;

class RegisterForm extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(
    FormBuilderInterface $builder,
    array $options
  ) {
    $builder
      ->add(
        'email',
        TextType::class,
        [
          'constraints' => [
            new NotBlank(),
            new Email()
          ],
          'required' => true,
          'attr' => ['class' => 'form-control']
        ]
      )
      ->add(
        'name',
        TextType::class,
        [
          'constraints' => [new NotBlank()],
          'required' => true,
          'attr' => ['class' => 'form-control']
        ]
      )
      ->add(
        'password',
        RepeatedType::class,
        [
          'type' => PasswordType::class,
          'required' => true,
          'constraints' => [
            new NotBlank(),
            new Length(array('min' => 6))
          ],
          'invalid_message' =>
          'Passwords do not match',
          'first_options'  => [
            'label' => 'Password',
            'attr'  => ['class' => 'form-control']
          ],
          'second_options' => [
            'label' => 'Confirm password',
            'attr'  => ['class' => 'form-control']
          ]
        ]
      )
      ->add(
        'submit',
        SubmitType::class,
        [
          'attr' => [
            'class' =>
            'form-control btn-primary pull-right'
          ],
          'label' => 'Save'
        ]
      );
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(
    OptionsResolver $resolver
  ) {
    $resolver->setDefaults([
      'data_class' => 'App\Entity\User'
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'register_form';
  }
}
