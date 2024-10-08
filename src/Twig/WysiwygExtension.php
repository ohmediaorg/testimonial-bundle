<?php

namespace OHMedia\TestimonialBundle\Twig;

use OHMedia\FileBundle\Service\FileManager;
use OHMedia\SettingsBundle\Service\Settings;
use OHMedia\TestimonialBundle\Entity\Testimonial;
use OHMedia\TestimonialBundle\Repository\TestimonialRepository;
use OHMedia\WysiwygBundle\Twig\AbstractWysiwygExtension;
use Symfony\Component\HttpFoundation\UrlHelper;
use Twig\Environment;
use Twig\TwigFunction;

class WysiwygExtension extends AbstractWysiwygExtension
{
    private array $schemas = [];

    public function __construct(
        private FileManager $fileManager,
        private UrlHelper $urlHelper,
        private Settings $settings,
        private TestimonialRepository $testimonialRepository
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('testimonial', [$this, 'testimonial'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
            new TwigFunction('testimonials', [$this, 'testimonials'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    public function testimonial(Environment $twig, ?int $id = null)
    {
        if (null === $id) {
            // try to get one at random
            $testimonials = $this->testimonialRepository->findAll();

            if ($testimonials) {
                shuffle($testimonials);

                $testimonial = $testimonials[0];
            } else {
                $testimonial = null;
            }
        } else {
            $testimonial = $this->testimonialRepository->find($id);
        }

        if (!$testimonial) {
            return '';
        }

        $rendered = $twig->render('@OHMediaTestimonial/testimonial.html.twig', [
            'testimonial' => $testimonial,
        ]);

        $rendered .= $this->getSchema($testimonial);

        return $rendered;
    }

    public function testimonials(Environment $twig)
    {
        $testimonials = $this->testimonialRepository
            ->createQueryBuilder('t')
            ->orderBy('t.ordinal', 'ASC')
            ->getQuery()
            ->getResult();

        if (!$testimonials) {
            return '';
        }

        $rendered = $twig->render('@OHMediaTestimonial/testimonials.html.twig', [
            'testimonials' => $testimonials,
        ]);

        foreach ($testimonials as $testimonial) {
            $rendered .= $this->getSchema($testimonial);
        }

        return $rendered;
    }

    private function getSchema(Testimonial $testimonial)
    {
        $id = $testimonial->getId();

        if (isset($this->schemas[$id])) {
            return '';
        }

        $this->schemas[$id] = true;

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Review',
            'author' => [
                '@type' => 'Person',
                'name' => $testimonial->getAuthor(),
            ],
            'reviewBody' => $testimonial->getQuote(),
            'reviewRating' => [
                '@type' => 'Rating',
                'bestRating' => Testimonial::RATING_MAX,
                'ratingValue' => $testimonial->getRating() * 1.0,
                'worstRating' => Testimonial::RATING_MIN,
            ],
        ];

        $organizationName = $this->settings->get('schema_organization_name');

        if ($organizationName) {
            $schema['itemReviewed'] = [
                '@type' => 'Organization',
                'name' => $organizationName,
            ];
        }

        return '<script type="application/ld+json">'.json_encode($schema).'</script>';
    }
}
