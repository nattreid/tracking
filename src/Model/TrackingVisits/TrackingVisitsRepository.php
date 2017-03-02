<?php

declare(strict_types = 1);

namespace NAttreid\Tracking\Model\TrackingVisits;

use DateTime;
use Generator;
use NAttreid\Orm\Repository;
use NAttreid\Tracking\Model\TrackingPages\TrackingPages;
use NAttreid\Utils\Range;
use Nextras\Dbal\Result\Result;
use Nextras\Orm\Collection\ICollection;

/**
 * TrackingVisits Repository
 *
 * @method ICollection|TrackingVisits[] findVisitsDays(Range $interval)
 * @method TrackingPages getByKey(DateTime $date)
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingVisitsRepository extends Repository
{

	/** @var TrackingVisitsMapper */
	protected $mapper;

	public static function getEntityClassNames()
	{
		return [TrackingVisits::class];
	}

	/**
	 * Vrati datum, ktere je treba prepocitat
	 * @param Range $interval
	 * @return DateTime[]|Generator
	 */
	public function findCalculateDate(Range $interval): Generator
	{
		return $this->mapper->findCalculateDate($interval);
	}

	/**
	 * Pocet navstev po hodinach ve dni
	 * @param Range $interval
	 * @return Result|null
	 */
	public function findVisitsHours(Range $interval)
	{
		return $this->mapper->findVisitsHours($interval);
	}

}
