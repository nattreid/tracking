<?php

declare(strict_types=1);

namespace NAttreid\Tracking\Model\ClickTrackingGroup;

use NAttreid\Orm\Repository;

/**
 * ClickTrackingGroup Repository
 *
 * @author Attreid <attreid@gmail.com>
 */
class ClickTrackingGroupRepository extends Repository
{

	public static function getEntityClassNames(): array
	{
		return [ClickTrackingGroup::class];
	}

	/**
	 * Vrati radek podle jmena
	 * @param string $name
	 * @return ClickTrackingGroup|null
	 */
	public function getByName(string $name): ?ClickTrackingGroup
	{
		return $this->getBy(['name' => $name]);
	}

}
