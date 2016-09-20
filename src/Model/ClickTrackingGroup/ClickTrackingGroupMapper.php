<?php

namespace NAttreid\Tracking\Model;
use NAttreid\Orm\Structure\Table;

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
