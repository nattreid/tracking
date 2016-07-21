<?php

namespace NAttreid\Tracking\Model;

use NAttreid\Utils\Range;

/**
 * TrackingVisits Repository
 *
 * @method ICollection|TrackingVisits[] findVisitsDays(\NAttreid\Utils\Range $interval)
 * @method TrackingPages getByKey(\Datetime $date) 
 * 
 * @author Attreid <attreid@gmail.com>
 */
class TrackingVisitsRepository extends \NAttreid\Orm\Repository {

    /** @var TrackingVisitsMapper */
    protected $mapper;

    public static function getEntityClassNames() {
        return [TrackingVisits::class];
    }

    /**
     * Vrati datum, ktere je treba prepocitat
     * @param Range $interval
     * @return \Datetime[]
     */
    public function findCalculateDate(Range $interval) {
        return $this->mapper->findCalculateDate($interval);
    }

    /**
     * Pocet navstev po hodinach ve dni
     * @param Range $interval
     * @return Result
     */
    public function findVisitsHours(Range $interval) {
        return $this->mapper->findVisitsHours($interval);
    }

}
