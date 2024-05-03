<?php

namespace OHMedia\TestimonialBundle\Twig;

use OHMedia\FileBundle\Service\FileManager;
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
        ];
    }

    public function testimonial(Environment $twig, int $id = null)
    {
        $testimonial = $this->testimonialRepository->find($id);

        if (!$testimonial) {
            return '';
        }

        $rendered = $twig->render('@OHMediaTestimonial/testimonial.html.twig', [
            'testimonial' => $testimonial,
        ]);

        if (!isset($this->schemas[$id])) {
            $this->schemas[$id] = true;

            $rendered .= $this->getSchema($testimonial);
        }

        return $rendered;
    }

    private function getSchema(Testimonial $testimonial)
    {
        $image = $testimonial->getImage();

        if ($image && $image->getPath()) {
            $path = $this->fileManager->getWebPath($testimonial->getImage());

            $thumbnailUrl = [$this->urlHelper->getAbsoluteUrl($path)];
        } elseif ($testimonial->getThumbnail()) {
            $thumbnailUrl = [$testimonial->getThumbnail()];
        } else {
            $thumbnailUrl = [];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'TestimonialObject',
            'name' => $testimonial->getTitle(),
            'thumbnailUrl' => $thumbnailUrl,
            'duration' => $testimonial->getDurationISO8601(),
            'contentUrl' => $testimonial->getUrl(),
            'embedUrl' => $testimonial->getEmbedUrl(),
        ];

        return '<script type="application/ld+json">'.json_encode($schema).'</script>';
    }
}
