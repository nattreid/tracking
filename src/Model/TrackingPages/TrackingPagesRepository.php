<?php

namespace NAttreid\Tracking\Model;

use Nextras\Orm\Collection\ICollection,
    NAttreid\Utils\Range;

/**
 * TrackingPages Repository
 *
 * @method ICollection|TrackingPages[] findPages(\NAttreid\Utils\Range $interval) 
 * @method TrackingPages getByKey(\Datetime $date, $page) 
 * 
 * @author Attreid <attreid@gmail.com>
 */
class TrackingPagesRepository extends \NAttreid\Orm\Repository {

    /** @var TrackingPagesMapper */
    protected $mapper;

    public static function getEntityClassNames() {
        return [TrackingPages::class];
    }

    /**
     * Vrati datum, ktere je treba prepocitat
     * @param Range $interval
     * @return \Datetime[]
     */
    public function findCalculateDate(Range $interval) {
        return $this->mapper->findCalculateDate($interval);
    }

}
