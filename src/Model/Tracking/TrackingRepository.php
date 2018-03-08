<?php

declare(strict_types=1);

namespace NAttreid\Tracking\Model\Tracking;

use NAttreid\Orm\Repository;
use NAttreid\Utils\Range;
use Nextras\Dbal\QueryException;
use Nextras\Dbal\Result\Result;
use stdClass;

/**
 * Tracking Repository
 *
 * @method Tracking getLatest($uid)
 *
 * @method int onlineUsers() Vrati online uzivatele
 * @method Result|null findVisitsHours(Range $interval, bool $useTime = false) Vrati navstevy po hodinach
 * @method stdClass[] findVisitPages(Range $interval) Navstevy jednotlivych stranek
 * @method void updateTimeOnPage(int $aiid, int $timeOnPage)
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingRepository extends Repository
{

	public static function getEntityClassNames(): array
	{
		return [Tracking::class];
	}
}
