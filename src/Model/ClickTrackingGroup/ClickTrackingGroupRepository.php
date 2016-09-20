<?php

namespace NAttreid\Tracking\Model;
use NAttreid\Orm\Repository;

/**
 * ClickTrackingGroup Repository
 *
 * @author Attreid <attreid@gmail.com>
 */
class ClickTrackingGroupRepository extends Repository
{

	public static function getEntityClassNames()
	{
		return [ClickTrackingGroup::class];
	}

	/**
	 * Vrati radek podle jmena
	 * @param string $name
	 * @return ClickTrackingGroup
	 */
	public function getByName($name)
	{
		return $this->getBy(['name' => $name]);
	}

}
