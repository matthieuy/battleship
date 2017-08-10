<?php

namespace NotificationBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class SMSRepository
 * @package NotificationBundle\Repository
 */
class SMSRepository extends EntityRepository
{
    /**
     * Get SMS to send and remove it
     * @return array
     */
    public function getAndRemoveSMS()
    {
        // Get all SMS
        $builder = $this->createQueryBuilder('sms');
        $builder
            ->select(['sms.id', 'sms.number', 'sms.message'])
            ->orderBy('sms.createDate', 'ASC');
        $list = $builder->getQuery()->getArrayResult();

        // Remove all
        if (count($list)) {
            $builder = $this->createQueryBuilder('sms');
            $builder->delete();
            $builder->getQuery()->execute();
        }

        // Return list
        return $list;
    }
}
