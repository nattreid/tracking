<?php

namespace NAttreid\Tracking\Model;

use DateTime;
use NAttreid\Orm\Structure\Table;
use NAttreid\Utils\Range;
use Nextras\Dbal\Result\Result;

/**
 * TrackingPages Mapper
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingPagesMapper extends Mapper
{

	/** @var boolean[] */
	private $isCalculated = [];

	protected function createTable(Table $table)
	{
		$table->addColumn('datefield')
			->date();
		$table->addColumn('page')
			->varChar();
		$table->addColumn('visits')
			->int();
		$table->addColumn('views')
			->int();
		$table->setPrimaryKey('datefield', 'page');
	}

	/**
	 * Navstevy jednotlivych stranek
	 * @param Range $interval
	 * @return Result
	 */
	public function findPages(Range $interval)
	{
		$builder = $this->builder()
			->select('[page], SUM([visits]) visits, SUM([views]) views')
			->andWhere('[datefield] BETWEEN DATE(%dt) AND DATE(%dt)', $interval->from, $interval->to)
			->groupBy('[page]')
			->addOrderBy('[visits] DESC, [views] DESC, [page]');
		return $this->execute($builder);
	}

	/**
	 * Vrati datum, ktere je treba prepocitat
	 * @param Range $interval
	 * @return DateTime[]
	 */
	public function findCalculateDate(Range $interval)
	{
		if (isset($this->isCalculated[(string)$interval])) {
			yield null;
		}
		$this->isCalculated[(string)$interval] = true;

		// dopocita posledni den
		if ($interval->to->format('Y-m-d') === (new DateTime)->format('Y-m-d')) {
			$last = $this->connection->query('SELECT MAX([datefield]) datefield FROM %table', $this->getTableName())->fetch();
			if ($last) {
				yield $last->datefield;
			}
		}

		// chybejici dny
		$dates = 'SELECT DATE_ADD(DATE(%dt), INTERVAL t4 + t16 + t64 + t256 + t1024 DAY) missingDate 
                    FROM 
                        (SELECT 0 t4    UNION ALL SELECT 1   UNION ALL SELECT 2   UNION ALL SELECT 3  ) t4,
                        (SELECT 0 t16   UNION ALL SELECT 4   UNION ALL SELECT 8   UNION ALL SELECT 12 ) t16,   
                        (SELECT 0 t64   UNION ALL SELECT 16  UNION ALL SELECT 32  UNION ALL SELECT 48 ) t64,      
                        (SELECT 0 t256  UNION ALL SELECT 64  UNION ALL SELECT 128 UNION ALL SELECT 192) t256,     
                        (SELECT 0 t1024 UNION ALL SELECT 256 UNION ALL SELECT 512 UNION ALL SELECT 768) t1024';

		$visits = 'SELECT DATE([datefield]) '
			. 'FROM %table '
			. 'WHERE DATE([datefield]) BETWEEN DATE(%dt) AND DATE(%dt)';

		$calculateDates = $this->connection->query('SELECT DATE([missingDate]) date FROM (' . $dates . ') dates '
			. 'WHERE [missingDate] NOT IN (' . $visits . ') '
			. 'AND [missingDate] <= DATE(%dt)', $interval->from, $this->getTableName(), $interval->from, $interval->to, $interval->to);

		foreach ($calculateDates as $date) {
			yield $date->date;
		}
	}

	/**
	 * Vrati entitu podle klice
	 * @param DateTime $date
	 * @param string $page
	 * @return TrackingPages
	 */
	public function getByKey(DateTime $date, $page)
	{
		$builder = $this->builder()
			->andWhere('[datefield] = DATE(%dt)', $date)
			->andWhere('[page] = %s', $page);
		return $this->fetch($builder);
	}

}
