<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\JobSeeker;
use App\Entity\Post;
use Burgov\Bundle\KeyValueFormBundle\Form\Type\KeyValueType;

class PostType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('title', TextType::class, [
        'constraints' => [
          new NotNull([
            'message' => 'title cannot be blank',
          ]),
          new Length([
            'min' => 5,
            'minMessage' => 'title is too short',
          ]),
        ]
      ])
      ->add('description', TextType::class, [
        'constraints' => [
          new NotNull([
            'message' => 'description cannot be blank',
          ]),
          new Length([
            'min' => 20,
            'minMessage' => 'description must be at least 100 caracters',
          ]),
        ]
      ])
      // ->add('tags', KeyValueType::class, array('value_type' => TextType::class))
      // ->add('tags', TextType::class)
      ->add('price', TextType::class, [
        'constraints' => [
          new NotNull([
            'message' => 'price cannot be blank',
          ]),
        ]
      ])
      ->add('save', SubmitType::class);
  }
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => Post::class,
      'csrf_protection' => false,
      'allow_extra_fields' => true
    ));
  }
}
