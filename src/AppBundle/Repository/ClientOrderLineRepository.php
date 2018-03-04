<?php

namespace AppBundle\Repository;
use AppBundle\Entity\ClientOrderLine;
use AppBundle\Entity\Shopper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * ClientOrderLineRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClientOrderLineRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface  $registry)
    {
        parent::__construct($registry, ClientOrderLine::class);
    }
}