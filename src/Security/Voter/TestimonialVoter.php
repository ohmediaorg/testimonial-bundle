<?php

namespace OHMedia\TestimonialBundle\Security\Voter;

use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;
use OHMedia\TestimonialBundle\Entity\Testimonial;

class TestimonialVoter extends AbstractEntityVoter
{
    public const INDEX = 'index';
    public const CREATE = 'create';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function getAttributes(): array
    {
        return [
            self::INDEX,
            self::CREATE,
            self::EDIT,
            self::DELETE,
        ];
    }

    protected function getEntityClass(): string
    {
        return Testimonial::class;
    }

    protected function canIndex(Testimonial $testimonial, User $loggedIn): bool
    {
        return true;
    }

    protected function canCreate(Testimonial $testimonial, User $loggedIn): bool
    {
        return true;
    }

    protected function canEdit(Testimonial $testimonial, User $loggedIn): bool
    {
        return true;
    }

    protected function canDelete(Testimonial $testimonial, User $loggedIn): bool
    {
        return true;
    }
}
