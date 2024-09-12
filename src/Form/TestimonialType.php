<?php

namespace OHMedia\TestimonialBundle\Form;

use OHMedia\FileBundle\Form\Type\FileEntityType;
use OHMedia\TestimonialBundle\Entity\Testimonial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestimonialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $testimonial = $options['data'];

        $builder->add('author');

        $builder->add('affiliation', TextType::class, [
            'required' => false,
        ]);

        $builder->add('quote', TextareaType::class, [
            'attr' => [
                'rows' => 5,
            ],
        ]);

        $builder->add('rating', NumberType::class, [
            'label' => sprintf(
                'Rating (%s-%s)',
                Testimonial::RATING_MIN,
                Testimonial::RATING_MAX
            ),
            'attr' => [
                'min' => Testimonial::RATING_MIN,
                'max' => Testimonial::RATING_MAX,
                'step' => 0.1,
            ],
            'html5' => true,
            'scale' => 1,
        ]);

        $builder->add('image', FileEntityType::class, [
            'image' => true,
            'data' => $testimonial->getImage(),
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Testimonial::class,
        ]);
    }
}
