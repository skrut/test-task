<?php
declare(strict_types=1);

namespace App\Database\Repositories;

use App\Database\Entities\MailChimp\MailChimpListMember;
use Doctrine\ORM\EntityRepository;

/**
 * Class MailChimpListMemberRepository
 * @package App\Database\Repositories
 */
class MailChimpListMemberRepository extends EntityRepository
{
    /**
     * @param $memberId
     * @param $listId
     * @return MailChimpListMember|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByListId($memberId, $listId): ?MailChimpListMember
    {
        $qb = $this->createQueryBuilder('m');
        $qb
            ->innerJoin('m.mailChimpList', 'l')
            ->andWhere('m.listMemberId = :memberId')
            ->andWhere('l.listId = :listId')
            ->setParameter('memberId', $memberId)
            ->setParameter('listId', $listId)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }
}
