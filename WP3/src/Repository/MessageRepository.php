<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
    * @return Message|null findBy(array $criteria, array $orderBy = null)
    */
    public function findById($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
    * @return Message|null findBy(array $criteria, array $orderBy = null)
    */
    public function findByKeywords($keywords)
    {
        return $this->createQueryBuilder('m')
            ->where('m.content LIKE :val')
            ->setParameter('val', '%' . $keywords . '%')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
    * @return Message findBy(array $criteria, array $orderBy = null)
    */
    public function findByCategoryKeywords($category, $keywords)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.category = :category')
            ->setParameter('keywords', '%' . $keywords . '%')
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
    * @return null 
    */
    public function doUpvote($id, $upvotes)
    {
        $upvotes++;
        return $this->createQueryBuilder('m')
            ->update($this->getEntityName(), 'm')
            ->set('m.upvotes', $upvotes)
            ->Where('m.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute()
        ;
    }

    /**
    * @return null 
    */
    public function doDownvote($id, $downvotes)
    {
        $downvotes++;
        return $this->createQueryBuilder('m')
            ->update($this->getEntityName(), 'm')
            ->set('m.downvotes', $downvotes)
            ->Where('m.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute()
        ;
    }
}
