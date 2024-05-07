# Installation

Update `composer.json` by adding this to the `repositories` array:

```json
{
    "type": "vcs",
    "url": "https://github.com/ohmediaorg/testimonial-bundle"
}
```

Then run `composer require ohmediaorg/testimonial-bundle:dev-main`.

Import the routes in `config/routes.yaml`:

```yaml
oh_media_testimonial:
    resource: '@OHMediaTestimonialBundle/config/routes.yaml'
```

Run `php bin/console make:migration` then run the subsequent migration.

# Frontend

Create `templates/bundles/OHMediaTestimonialBundle/testimonial.html.twig` and
`templates/bundles/OHMediaTestimonialBundle/testimonials.html.twig`, which are
expected for rendering the WYSIWYG Twig functions `{{ testimonial(id) }}` and
`{{ testimonials() }}`.

The `Testimonial` entity has required fields `author` and `quote`, and optional
fields `affiliation` and `image`. The `image` is an `OHMedia\FileBundle\Entity\File`
and should be rendered with the `image_tag` Twig function.
