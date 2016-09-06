<?php

namespace NAttreid\Tracking\Model;

/**
 * ClickTrackingGroup Mapper
 *
 * @author Attreid <attreid@gmail.com>
 */
class ClickTrackingGroupMapper extends Mapper
{

	protected function createTable(\NAttreid\Orm\Structure\Table $table)
	{
		$table->addPrimaryKey('id')
			->int()
			->setAutoIncrement();
		$table->addColumn('name')
			->varChar(150)
			->setUnique();
	}

}
