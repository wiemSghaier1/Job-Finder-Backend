<?php

namespace App\Repository;

use App\Entity\Employeur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Employeur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employeur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employeur[]    findAll()
 * @method Employeur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeurRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Employeur::class);
        $this->manager = $manager;
    }
    public function updateEmployeur(Employeur $employeur, $data)
    {
        isset($data['isCompany']) ?  $isCompany = $data['isCompany'] : $isCompany = null;

        empty($data['fullName']) ? true : $employeur->setFullName($data['fullName']);
        is_null($isCompany) ? true : $employeur->setIsCompany($data['isCompany']);
        empty($data['phoneNumber']) ? true : $employeur->setPhoneNumber($data['phoneNumber']);
        $this->manager->flush();
    }
    // /**
    //  * @return Employeur[] Returns an array of Employeur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Employeur
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
