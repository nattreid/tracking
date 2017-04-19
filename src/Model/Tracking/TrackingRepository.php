<?php

declare(strict_types=1);

namespace NAttreid\Tracking\Model\Tracking;

use NAttreid\Orm\Repository;
use NAttreid\Utils\Range;
use Nextras\Dbal\Result\Result;
use stdClass;

/**
 * Tracking Repository
 *
 * @method Tracking getLatest($uid)
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingRepository extends Repository
{

	/** @var TrackingMapper */
	protected $mapper;

	public static function getEntityClassNames(): array
	{
		return [Tracking::class];
	}

	/**
	 * Vrati pocet online uzivatelu
	 * @return int
	 */
	public function onlineUsers(): int
	{
		return $this->mapper->findCountOnlineUsers()->fetch()->count;
	}

	/**
	 * Vrati navstevy po hodinach
	 * @param Range $interval
	 * @param bool $useTime ma se pouzit cas v intervalu
	 * @return Result|null
	 */
	public function findVisitsHours(Range $interval, bool $useTime = false): ?Result
	{
		return $this->mapper->findVisitsHours($interval, $useTime);
	}

	/**
	 * Navstevy jednotlivych stranek
	 * @param Range $interval
	 * @return stdClass[]
	 */
	public function findVisitPages(Range $interval): array
	{
		return $this->mapper->findVisitPages($interval);
	}

}
