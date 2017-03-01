<?php

declare(strict_types = 1);

namespace NAttreid\Tracking\Model\ClickTrackingGroup;

use NAttreid\Orm\Structure\Table;
use NAttreid\Tracking\Model\Mapper;

/**
 * ClickTrackingGroup Mapper
 *
 * @author Attreid <attreid@gmail.com>
 */
class ClickTrackingGroupMapper extends Mapper
{

	protected function createTable(Table $table)
	{
		$table->addPrimaryKey('id')
			->int()
			->setAutoIncrement();
		$table->addColumn('name')
			->varChar(150)
			->setUnique();
	}

}
