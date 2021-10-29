<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Employeur;

class EmployeurType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('fullName', TextType::class, ['constraints' => [
        new NotNull([
          'message' => 'full name cant be blank',
        ]),

      ]])
      ->add('isCompany', ChoiceType::class, [
        'choices' => [
          1 => true,
          0 => false
        ],
        'constraints' => [
          new NotNull([
            'message' => 'user type cant be blank',
          ]),

        ]
      ])->add('password', TextType::class, [
        'constraints' => [
          new NotNull([
            'message' => 'password cannot be blank',
          ]),
          new Length([
            'min' => 5,
            'minMessage' => 'password is too short',
          ]),
        ]
      ])
      ->add('phoneNumber', TextType::class)
      ->add('email', EmailType::class, [
        'constraints' => [
          new NotNull([
            'message' => 'Email cannot be blank',
          ]),
          new Email(),
        ]
      ])
      ->add('save', SubmitType::class);
  }
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => Employeur::class,
      'csrf_protection' => false
    ));
  }
}
