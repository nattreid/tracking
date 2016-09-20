<?php

namespace NAttreid\Tracking\Model;

use DateTime;
use NAttreid\Orm\Structure\Table;
use NAttreid\Utils\Range;
use Nextras\Dbal\Result\Result;

/**
 * TrackingVisits Mapper
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingVisitsMapper extends Mapper
{

	/** @var boolean[] */
	private $isCalculated = [];

	protected function createTable(Table $table)
	{
		$table->addPrimaryKey('datefield')
			->datetime();
		$table->addColumn('visits')
			->int();
	}

	/**
	 * Pocet navstev po dnech
	 * @param Range $interval
	 * @return Result
	 */
	public function findVisitsDays(Range $interval)
	{
		$builder = $this->builder()
			->select('DATE([datefield]) datefield, SUM([visits]) visits')
			->andWhere('DATE([datefield]) BETWEEN DATE(%dt) AND DATE(%dt)', $interval->from, $interval->to)
			->addGroupBy('DATE([datefield])');
		return $this->execute($builder);
	}

	/**
	 * Pocet navstev po hodinach ve dni
	 * @param Range $interval
	 * @return Result
	 */
	public function findVisitsHours(Range $interval)
	{
		$diffBuilder = $this->builder()
			->select('DATEDIFF(MAX([datefield]), MIN([datefield])) diff')
			->andWhere('DATE([datefield]) BETWEEN DATE(%dt) AND DATE(%dt)', $interval->from, $interval->to);
		$diff = $this->execute($diffBuilder)->fetch();

		if ($diff && !empty($diff->diff)) {
			$diff = $diff->diff;
		} else {
			$diff = 1;
		}

		$builder = $this->builder()
			->select('HOUR([datefield]) hour, ROUND(SUM([visits]) / %i, 2) visits', $diff)
			->andWhere('DATE([datefield]) BETWEEN DATE(%dt) AND DATE(%dt)', $interval->from, $interval->to)
			->addGroupBy('[hour]');
		return $this->execute($builder);
	}/** @noinspection PhpInconsistentReturnPointsInspection */

	/**
	 * Vrati datum, ktere je treba prepocitat
	 * @param Range $interval
	 * @return DateTime[]
	 */
	public function findCalculateDate(Range $interval)
	{
		if (isset($this->isCalculated[(string)$interval])) {
			return [];
		}
		$this->isCalculated[(string)$interval] = TRUE;

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
	 * @return TrackingVisits
	 */
	public function getByKey(DateTime $date)
	{
		$builder = $this->builder()
			->andWhere('[datefield] = %dt', $date);
		return $this->toCollection($builder)->fetch();
	}

}
