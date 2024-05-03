<?php

namespace OHMedia\TestimonialBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use OHMedia\TestimonialBundle\Entity\Testimonial;

/**
 * @method Testimonial|null find($id, $lockMode = null, $lockVersion = null)
 * @method Testimonial|null findOneBy(array $criteria, array $orderBy = null)
 * @method Testimonial[]    findAll()
 * @method Testimonial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestimonialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Testimonial::class);
    }

    public function save(Testimonial $testimonial, bool $flush = false): void
    {
        $this->getEntityManager()->persist($testimonial);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Testimonial $testimonial, bool $flush = false): void
    {
        $this->getEntityManager()->remove($testimonial);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
