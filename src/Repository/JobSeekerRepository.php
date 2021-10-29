<?php

namespace App\Repository;

use App\Entity\JobSeeker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method JobSeeker|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobSeeker|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobSeeker[]    findAll()
 * @method JobSeeker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobSeekerRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, JobSeeker::class);
        $this->manager = $manager;
    }
    public function updatejobSeeker(JobSeeker $jobSeeker, $data)
    {

        empty($data['firstName']) ? true : $jobSeeker->setfirstName($data['firstName']);
        empty($data['lastName']) ? true : $jobSeeker->setlastName($data['lastName']);
        empty($data['phoneNumber']) ? true : $jobSeeker->setPhoneNumber($data['phoneNumber']);
        $this->manager->flush();
    }

    // /*
    //   * @return JobSeeker[] Returns an array of JobSeeker objects
    //   */

    // public function findByExampleField($value)
    // {
    //     return $this->createQueryBuilder('j')
    //         ->andWhere('j.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->orderBy('j.id', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult();
    // }

    /*
    public function findOneBySomeField($value): ?JobSeeker
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
