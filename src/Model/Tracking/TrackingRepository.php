<?php

namespace NAttreid\Tracking\Model;

use NAttreid\Utils\Range;
use Nextras\Dbal\Result\Result;

/**
 * Tracking Repository
 *
 * @method Tracking getLatest($uid)
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingRepository extends \NAttreid\Orm\Repository
{

	/** @var TrackingMapper */
	protected $mapper;

	public static function getEntityClassNames()
	{
		return [Tracking::class];
	}

	/**
	 * Vrati pocet online uzivatelu
	 * @return int
	 */
	public function onlineUsers()
	{
		return $this->mapper->findCountOnlineUsers()->fetch()->count;
	}

	/**
	 * Vrati navstevy po hodinach
	 * @param Range $interval
	 * @param boolean $useTime ma se pouzit cas v intervalu
	 * @return Result
	 */
	public function findVisitsHours(Range $interval, $useTime = FALSE)
	{
		return $this->mapper->findVisitsHours($interval, $useTime);
	}

	/**
	 * Navstevy jednotlivych stranek
	 * @param Range $interval
	 * @return \stdClass[]
	 */
	public function findVisitPages(Range $interval)
	{
		return $this->mapper->findVisitPages($interval);
	}

}
