<?php

namespace NAttreid\Tracking\Model;

use NAttreid\Orm\Structure\Table;
use NAttreid\Utils\Range;
use Nette\Http\Url;
use Nextras\Dbal\QueryBuilder\QueryBuilder;
use Nextras\Dbal\Result\Result;

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

	public function setup($minTimeBetweenVisits, $onlineTime)
	{
		$this->minTimeBetweenVisits = $minTimeBetweenVisits;
		$this->onlineTime = $onlineTime;
	}

	protected function createTable(Table $table)
	{
		$table->addColumn('id')
			->int()
			->setAutoIncrement();
		$table->addColumn('uid')
			->char(36)
			->setKey();
		$table->addColumn('inserted')
			->datetime()
			->setKey();
		$table->addColumn('url')
			->varChar()
			->setKey();
		$table->addColumn('referer')
			->varChar();
		$table->addColumn('ip')
			->varChar(16)
			->setDefault(null)
			->setKey();
		$table->addColumn('browser')
			->varChar(30)
			->setDefault(null);
		$table->addColumn('timeOnPage')
			->int()
			->setDefault(null);
		$table->addColumn('utmSource')
			->varChar(50)
			->setDefault(null);
		$table->addColumn('utmMedium')
			->varChar(50)
			->setDefault(null);
		$table->addColumn('utmCampaign')
			->varChar(50)
			->setDefault(null);
		$table->addKey('uid', 'inserted');
		$table->setPrimaryKey('id', 'inserted');
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
	 * @return QueryBuilder
	 */
	public function getLatest($uid)
	{
		$builder = $this->builder()
			->andWhere('[uid] = %s', $uid)
			->orderBy('inserted DESC')
			->limitBy(1);
		return $this->toCollection($builder)->fetch();
	}

	/**
	 * Vrati online uzivatele
	 * @return Result
	 */
	public function findCountOnlineUsers()
	{
		$builder = $this->builder()
			->addSelect('COUNT(DISTINCT([uid])) count')
			->andWhere('[inserted] > %dt', (new \DateTime)->modify('-' . $this->onlineTime . ' minute'))
			->andWhere('[timeOnPage] IS NOT null');
		return $this->execute($builder);
	}

	/**
	 * Vrati navstevy po hodinach
	 * @param Range $interval
	 * @param boolean $useTime ma se pouzit cas v intervalu
	 * @return Result
	 */
	public function findVisitsHours(Range $interval, $useTime = false)
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
	 * @return \stdClass[]
	 */
	public function findVisitPages(Range $interval)
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
			$data = new \stdClass;
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

}
