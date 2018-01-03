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
 * @method ICollection|TrackingPages[] findPages(Range $interval)
 * @method TrackingPages getByKey(DateTime $date, $page)
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingPagesRepository extends Repository
{

	/** @var TrackingPagesMapper */
	protected $mapper;

	public static function getEntityClassNames(): array
	{
		return [TrackingPages::class];
	}

	/**
	 * Vrati datum, ktere je treba prepocitat
	 * @param Range $interval
	 * @return DateTime[]
	 * @throws QueryException
	 */
	public function findCalculateDate(Range $interval): array
	{
		return $this->mapper->findCalculateDate($interval);
	}

}
