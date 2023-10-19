<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Enum\Etat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function filtrerSortie(Participant $user,EntityManagerInterface $entityManager, string $nom,Site $site,\DateTime $dateDebut, \DateTime $dateFin,array $checkboxValues, string $etat ): array
    {
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('s,p,o')
            ->from('App:Sortie', 's')
            ->leftJoin('s.participantsInscrits', 'p')
            ->leftJoin('s.organisateur', 'o')
            ->where('s.site = :site')
            ->setParameter('site', $site->getId());
        if($nom != "[No_Name]")
        {
            $queryBuilder->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%' . $nom . '%');
        }

        if($dateDebut != New \DateTime('2001-01-01 00:00:00') && $dateFin != New \DateTime('2001-01-01 00:00:00'))
        {
            $queryBuilder->andWhere('s.dateHeureDebut BETWEEN :dateDebut AND :dateFin')
                ->setParameter(':dateDebut', $dateDebut)
                ->setParameter(':dateFin', $dateFin);
        }
        if (in_array('isInscrit',$checkboxValues) && in_array('isNotInscrit',$checkboxValues))
        {

        }
        elseif(in_array('isInscrit', $checkboxValues) && !in_array('isNotInscrit',$checkboxValues))
        {
            $queryBuilder->andWhere(':user MEMBER OF s.participantsInscrits')
                ->setParameter('user',$user->getId());
        }
        elseif (!in_array('isInscrit', $checkboxValues) && in_array('isNotInscrit',$checkboxValues))
        {
            $queryBuilder->andWhere(':user NOT MEMBER OF s.participantsInscrits')
                ->setParameter('user',$user->getId());
        }
        if (in_array('isOrganisateur',$checkboxValues))
        {
            $queryBuilder->andWhere('s.organisateur = :user')
                ->setParameter('user',$user->getId());
        }

        if ($etat != 'Choisir une option')
        {
            $queryBuilder->andWhere('s.etat = :etat')
                ->setParameter('etat',$etat);
        }



        $query = $queryBuilder->getQuery();
       // dd($query->getArrayResult());
        return $query->getArrayResult();
    }
}
