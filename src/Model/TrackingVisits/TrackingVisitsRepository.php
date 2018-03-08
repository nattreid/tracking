<?php

declare(strict_types=1);

namespace NAttreid\Tracking\Model\TrackingVisits;

use DateTime;
use NAttreid\Orm\Repository;
use NAttreid\Tracking\Model\TrackingPages\TrackingPages;
use NAttreid\Utils\Range;
use Nextras\Dbal\QueryException;
use Nextras\Dbal\Result\Result;
use Nextras\Orm\Collection\ICollection;

/**
 * TrackingVisits Repository
 *
 * @method ICollection|TrackingVisits[] findVisitsDays(Range $interval)
 * @method TrackingVisits getByKey(DateTime $date)
 * @method DateTime[] findCalculateDate(Range $interval) Vrati datum, ktere je treba prepocitat
 * @method Result|null findVisitsHours(Range $interval) Pocet navstev po hodinach ve dni
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingVisitsRepository extends Repository
{

	public static function getEntityClassNames(): array
	{
		return [TrackingVisits::class];
	}
}
