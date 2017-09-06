<?php


namespace BackendBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class CurrencyRepository extends EntityRepository
{
    public function getCurrencyById($value)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.name')
            ->where('a.name = :name')
            ->setParameter('name', $value);

        return $qb->getQuery()
            ->getResult();
    }

    public function getCurrencyName()
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b.name');

        return $qb->getQuery()
            ->getResult();
    }


    public function getAllCurrenciesInAssocArray()
    {
        $query = $this->createQueryBuilder('a')
            ->select('a.name')
            ->indexBy('a', 'a.name')
            ->getQuery()
            ->getResult();

        return $query;
    }
}