<?php

declare(strict_types=1);

namespace NAttreid\Tracking\Model\Tracking;

use NAttreid\Orm\Structure\Table;
use NAttreid\Tracking\Model\Mapper;
use NAttreid\Utils\Range;
use Nette\Http\Url;
use Nextras\Dbal\QueryException;
use Nextras\Dbal\Result\Result;
use Nextras\Orm\Entity\IEntity;
use stdClass;

/**
 * Tracking Mapper
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingMapper extends Mapper
{

	/** @var int */
	private $minTimeBetweenVisits;

	/** @var int */
	private $onlineTime;

	public function setup(int $minTimeBetweenVisits, int $onlineTime): void
	{
		$this->minTimeBetweenVisits = $minTimeBetweenVisits;
		$this->onlineTime = $onlineTime;
	}

	protected function createTable(Table $table): void
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
		$table->addColumn('url')
			->text();
		$table->addColumn('referer')
			->text();
		$table->addColumn('ip')
			->varChar(40)
			->setDefault(null)
			->setKey();
		$table->addColumn('browser')
			->text()
			->setDefault(null);
		$table->addColumn('timeOnPage')
			->int()
			->setDefault(null);
		$table->addColumn('utmSource')
			->varChar(100)
			->setDefault(null);
		$table->addColumn('utmMedium')
			->varChar(100)
			->setDefault(null);
		$table->addColumn('utmCampaign')
			->varChar(100)
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
	 * Vrati posledni navstivenou stranku
	 * @param string $uid
	 * @return Tracking|null
	 */
	public function getLatest(string $uid): ?Tracking
	{
		$builder = $this->builder()
			->andWhere('[uid] = %s', $uid)
			->orderBy('inserted DESC')
			->limitBy(1);
		return $this->toEntity($builder);
	}

	/**
	 * Vrati online uzivatele
	 * @return int
	 * @throws QueryException
	 */
	public function onlineUsers(): int
	{
		$builder = $this->builder()
			->addSelect('COUNT(DISTINCT([uid])) count')
			->andWhere('[inserted] > %dt', (new \DateTime)->modify('-' . $this->onlineTime . ' minute'))
			->andWhere('[timeOnPage] IS NOT null');
		return $this->execute($builder)->fetch()->count;
	}

	/**
	 * Vrati navstevy po hodinach
	 * @param Range $interval
	 * @param bool $useTime ma se pouzit cas v intervalu
	 * @return Result|null
	 * @throws QueryException
	 */
	public function findVisitsHours(Range $interval, bool $useTime = false): ?Result
	{
		$date = $useTime ? '%dt' : 'DATE(%dt)';

		$subQuery = 'SELECT '
			. 'DATE_FORMAT([inserted], "%%Y-%%m-%%d %%H:00:00") datefield, '
			. 'COUNT([uid]) visits '
			. 'FROM %table '
			. 'WHERE ' . ($useTime ? '[inserted]' : 'DATE([inserted])') . ' BETWEEN ' . $date . ' AND ' . $date . ' '
			. 'GROUP BY [uid], ROUND(UNIX_TIMESTAMP([inserted]) / %i)';
		return $this->connection->query('SELECT [datefield], COUNT([visits]) visits FROM (' . $subQuery . ') sub GROUP BY HOUR([datefield]), DAY([datefield])', $this->getTableName(), $interval->from, $interval->to, $this->minTimeBetweenVisits * 60);
	}

	/**
	 * Navstevy jednotlivych stranek
	 * @param Range $interval
	 * @return stdClass[]
	 * @throws QueryException
	 */
	public function findVisitPages(Range $interval): array
	{
		$result = [];
		$subQuery = 'SELECT '
			. '[url], '
			. 'DATE([inserted]) datefield, '
			. 'COUNT([uid]) num '
			. 'FROM %table '
			. 'WHERE DATE([inserted]) BETWEEN DATE(%dt) AND DATE(%dt) '
			. 'GROUP BY [url], [uid], ROUND(UNIX_TIMESTAMP([inserted]) / %i)';
		$rows = $this->connection->query('SELECT [datefield], [url], COUNT([num]) visits, SUM([num]) views FROM (' . $subQuery . ') sub GROUP BY [url]', $this->getTableName(), $interval->from, $interval->to, $this->minTimeBetweenVisits * 60);

		foreach ($rows as $row) {
			$data = new stdClass;
			$data->datefield = $row->datefield;
			$data->visits = $row->visits;
			$data->views = $row->views;

			$url = new Url($row->url);
			$data->page = substr($url->getPath(), 1) ?: '';
			if (!empty($url->query)) {
				$data->page .= '?' . $url->getQuery();
			}
			if (!empty($url->fragment)) {
				$data->page .= '#' . $url->getFragment();
			}
			$result[] = $data;
		}
		return $result;
	}

	/**
	 * @param IEntity|Tracking $entity
	 * @throws QueryException
	 */
	public function persist(IEntity $entity): void
	{
		if (!$entity->isPersisted()) {
			parent::persist($entity);
		} else {
			$this->connection->query('UPDATE %table SET %set WHERE [aiid] = %i',
				$this->getTableName(), [
					'uid' => $entity->uid,
					'inserted' => $entity->inserted,
					'url' => $entity->url,
					'referer' => $entity->referer,
					'ip' => $entity->ip,
					'browser' => $entity->browser,
					'timeOnPage' => $entity->timeOnPage,
					'utmSource' => $entity->utmSource,
					'utmMedium' => $entity->utmMedium,
					'utmCampaign' => $entity->utmCampaign,
				],
				$entity->aiid);
		}
	}
}
