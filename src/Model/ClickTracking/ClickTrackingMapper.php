<?php

namespace NAttreid\Tracking\Model;

use NAttreid\Orm\Structure\Table;
use NAttreid\Utils\Range;
use Nextras\Dbal\Result\Result;

/**
 * ClickTracking Mapper
 *
 * @author Attreid <attreid@gmail.com>
 */
class ClickTrackingMapper extends Mapper
{

	protected function createTable(Table $table)
	{
		$table->addColumn('aiid')
			->int()
			->setAutoIncrement();
		$table->addColumn('uid')
			->char(36)
			->setKey();
		$table->addColumn('inserted')
			->datetime()
			->setKey();
		$table->addColumn('groupId')
			->int()
			->setKey();
		$table->addColumn('ip')
			->varChar(16)
			->setDefault(null)
			->setKey();
		$table->addColumn('browser')
			->varChar(30)
			->setDefault(null);
		$table->addColumn('value')
			->varChar(50)
			->setDefault(null);
		$table->addColumn('averageValue')
			->float(13, 2)
			->setDefault(null);
		$table->addColumn('sumValue')
			->float(13, 2)
			->setDefault(null);
		$table->addKey('uid', 'inserted');
		$table->setPrimaryKey('aiid', 'inserted');
		$table->add('!50100 PARTITION BY RANGE ( YEAR(inserted))
                        (PARTITION y2014 VALUES LESS THAN (2015) ENGINE = InnoDB,
                         PARTITION y2015 VALUES LESS THAN (2016) ENGINE = InnoDB,
                         PARTITION y2016 VALUES LESS THAN (2017) ENGINE = InnoDB,
                         PARTITION y2017 VALUES LESS THAN (2018) ENGINE = InnoDB,
                         PARTITION y2018 VALUES LESS THAN (2019) ENGINE = InnoDB,
                         PARTITION y2019 VALUES LESS THAN (2020) ENGINE = InnoDB,
                         PARTITION y2020 VALUES LESS THAN (2021) ENGINE = InnoDB,
                         PARTITION y2021 VALUES LESS THAN (2022) ENGINE = InnoDB,
                         PARTITION y2022 VALUES LESS THAN (2023) ENGINE = InnoDB,
                         PARTITION y2023 VALUES LESS THAN (2024) ENGINE = InnoDB,
                         PARTITION y2024 VALUES LESS THAN (2025) ENGINE = InnoDB)');
	}

	/**
	 * Pocet kliku po dnech
	 * @param int $groupId
	 * @param Range $interval
	 * @return Result
	 */
	public function findClicksByDay($groupId, Range $interval)
	{
		$builder = $this->builder()
			->select('DATE([inserted]) date, COUNT([uid]) num, SUM([sumValue]) sum, AVG([averageValue]) avg')
			->andWhere('[groupId] = %i', $groupId)
			->andWhere('DATE([inserted]) BETWEEN %dt AND %dt', $interval->from, $interval->to)
			->groupBy('[date]');
		return $this->execute($builder);
	}

}
