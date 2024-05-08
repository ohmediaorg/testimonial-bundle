<?php

namespace OHMedia\TestimonialBundle\Service;

use OHMedia\BackendBundle\Shortcodes\AbstractShortcodeProvider;
use OHMedia\BackendBundle\Shortcodes\Shortcode;
use OHMedia\TestimonialBundle\Repository\TestimonialRepository;

class TestimonialShortcodeProvider extends AbstractShortcodeProvider
{
    public function __construct(private TestimonialRepository $testimonialRepository)
    {
    }

    public function getTitle(): string
    {
        return 'Testimonials';
    }

    public function buildShortcodes(): void
    {
        $this->addShortcode(new Shortcode('All Testimonials', 'testimonials()'));
        $this->addShortcode(new Shortcode('One Random Testimonial', 'testimonial()'));

        $testimonials = $this->testimonialRepository->createQueryBuilder('t')
            ->orderBy('t.author', 'asc')
            ->getQuery()
            ->getResult();

        foreach ($testimonials as $testimonial) {
            $id = $testimonial->getId();

            $this->addShortcode(new Shortcode(
                sprintf('%s (ID:%s)', $testimonial, $id),
                'testimonial('.$id.')'
            ));
        }
    }
}
