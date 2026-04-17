<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('content', TextareaType::class, [
            'label' => 'Contenu',
            'constraints' => [
                new NotBlank(message: 'Le contenu ne peut pas être vide.'),
                new Length(
                    min: 5,
                    minMessage: 'Le contenu doit faire au moins {{ limit }} caractères.',
                ),
            ],
            'attr' => ['rows' => 5, 'placeholder' => 'Quoi de neuf ?'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
