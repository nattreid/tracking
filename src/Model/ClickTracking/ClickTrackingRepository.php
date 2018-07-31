<?php

declare(strict_types=1);

namespace NAttreid\Tracking\Model\ClickTracking;

use NAttreid\Orm\Repository;
use NAttreid\Utils\Range;
use Nextras\Dbal\Result\Result;

/**
 * ClickTracking Repository
 *
 * @method Result|null findClicksByDay(int $groupId, Range $interval)  Pocet kliku po dnech
 * @method Result|null findClicksByValue(int $groupId, Range $interval)  Pocet kliku podle hodnoty
 *
 * @author Attreid <attreid@gmail.com>
 */
class ClickTrackingRepository extends Repository
{

	public static function getEntityClassNames(): array
	{
		return [ClickTracking::class];
	}
}
