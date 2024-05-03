<?php

namespace OHMedia\TestimonialBundle\Service;

use OHMedia\BackendBundle\Service\AbstractNavItemProvider;
use OHMedia\BootstrapBundle\Component\Nav\NavItemInterface;
use OHMedia\BootstrapBundle\Component\Nav\NavLink;
use OHMedia\TestimonialBundle\Entity\Testimonial;
use OHMedia\TestimonialBundle\Security\Voter\TestimonialVoter;

class TestimonialNavItemProvider extends AbstractNavItemProvider
{
    public function getNavItem(): ?NavItemInterface
    {
        if ($this->isGranted(TestimonialVoter::INDEX, new Testimonial())) {
            return (new NavLink('Testimonials', 'testimonial_index'))
                ->setIcon('megaphone-fill');
        }

        return null;
    }
}
