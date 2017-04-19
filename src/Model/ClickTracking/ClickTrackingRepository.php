<?php

declare(strict_types=1);

namespace NAttreid\Tracking\Model\ClickTracking;

use NAttreid\Orm\Repository;
use NAttreid\Utils\Range;
use Nextras\Dbal\Result\Result;

/**
 * ClickTracking Repository
 *
 * @author Attreid <attreid@gmail.com>
 */
class ClickTrackingRepository extends Repository
{

	/** @var ClickTrackingMapper */
	protected $mapper;

	public static function getEntityClassNames(): array
	{
		return [ClickTracking::class];
	}

	/**
	 * Pocet kliku po dnech
	 * @param int $groupId
	 * @param Range $interval
	 * @return Result|null
	 */
	public function findClicksByDay(int $groupId, Range $interval): ?Result
	{
		return $this->mapper->findClicksByDay($groupId, $interval);
	}

}
