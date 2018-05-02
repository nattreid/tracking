<?php

declare(strict_types=1);

namespace NAttreid\Tracking\Model\TrackingPages;

use DateTime;
use Generator;
use NAttreid\Orm\Repository;
use NAttreid\Utils\Range;
use Nextras\Dbal\QueryException;
use Nextras\Orm\Collection\ICollection;

/**
 * TrackingPages Repository
 *
 * @method Result|null findPages(Range $interval)
 * @method TrackingPages getByKey(DateTime $date, string $page)
 * @method DateTime[] findCalculateDate(Range $interval) Vrati datum, ktere je treba prepocitat
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingPagesRepository extends Repository
{

	public static function getEntityClassNames(): array
	{
		return [TrackingPages::class];
	}
}
