<?php

namespace OHMedia\TestimonialBundle\Service;

use OHMedia\SecurityBundle\Service\EntityChoiceInterface;
use OHMedia\TestimonialBundle\Entity\Testimonial;

class TestimonialEntityChoice implements EntityChoiceInterface
{
    public function getLabel(): string
    {
        return 'Testimonials';
    }

    public function getEntities(): array
    {
        return [Testimonial::class];
    }
}
