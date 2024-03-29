<?php

declare(strict_types=1);

namespace NAttreid\Tracking\Model\TrackingVisits;

use DateTime;
use DateTimeInterface;
use NAttreid\Orm\Structure\Table;
use NAttreid\Tracking\Model\Mapper;
use NAttreid\Utils\Range;
use Nextras\Dbal\QueryException;
use Nextras\Dbal\Result\Result;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;

/**
 * TrackingVisits Mapper
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingVisitsMapper extends Mapper
{

	/** @var bool[] */
	private $isCalculated = [];

	protected function createTable(Table $table): void
	{
		$table->addPrimaryKey('datefield')
			->datetime();
		$table->addColumn('visits')
			->int();
	}

	/**
	 * Pocet navstev po dnech
	 * @param Range $interval
	 * @return Result|null
	 * @throws QueryException
	 */
	public function findVisitsDays(Range $interval): ?Result
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
	 * @return Result|null
	 * @throws QueryException
	 */
	public function findVisitsHours(Range $interval): ?Result
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
	}

	/**
	 * Vrati datum, ktere je treba prepocitat
	 * @param Range $interval
	 * @return DateTime[]
	 * @throws QueryException
	 */
	public function findCalculateDate(Range $interval): array
	{
		$result = [];
		if (!isset($this->isCalculated[(string)$interval])) {
			$this->isCalculated[(string)$interval] = true;

			// dopocita posledni den
			if ($interval->to->format('Y-m-d') >= (new DateTime)->format('Y-m-d')) {
				$last = $this->connection->query('SELECT MAX([datefield]) datefield FROM %table', $this->getTableName())->fetch();
				if ($last) {
					$result[] = $last->datefield;
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

			if ($calculateDates) {
				foreach ($calculateDates as $date) {
					$result[] = $date->date;
				}
			}
		}
		return $result;
	}

	/**
	 * Vrati entitu podle klice
	 * @param DateTimeInterface $date
	 * @return IEntity|TrackingVisits|null
	 */
	public function getByKey(DateTimeInterface $date): ?TrackingVisits
	{
		$builder = $this->builder()
			->andWhere('[datefield] = %dts', $date);
		return $this->toEntity($builder);
	}

	/**
	 * @param IEntity|TrackingVisits $entity
	 * @throws QueryException
	 */
	public function persist(IEntity $entity): void
	{
		if (!$entity->isPersisted()) {
			parent::persist($entity);
		} else {
			$this->connection->query('UPDATE %table SET %set WHERE [datefield] = %dts',
				$this->getTableName(), [
					'visits' => $entity->visits
				],
				$entity->datefield);
		}
	}

}
