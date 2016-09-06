<?php

namespace NAttreid\Tracking\Model;

use NAttreid\Utils\Range;
use Nextras\Dbal\Result\Result;

/**
 * ClickTracking Repository
 *
 * @author Attreid <attreid@gmail.com>
 */
class ClickTrackingRepository extends \NAttreid\Orm\Repository
{

	/** @var ClickTrackingMapper */
	protected $mapper;

	public static function getEntityClassNames()
	{
		return [ClickTracking::class];
	}

	/**
	 * Pocet kliku po dnech
	 * @param int $groupId
	 * @param Range $interval
	 * @return Result
	 */
	public function findClicksByDay($groupId, Range $interval)
	{
		return $this->mapper->findClicksByDay($groupId, $interval);
	}

}
