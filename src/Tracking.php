<?php

declare(strict_types=1);

namespace NAttreid\Tracking;

use NAttreid\Security\User;
use NAttreid\Tracking\Model\ClickTracking\ClickTracking;
use NAttreid\Tracking\Model\ClickTrackingGroup\ClickTrackingGroup;
use NAttreid\Tracking\Model\Orm;
use NAttreid\Tracking\Model\Tracking\Tracking as TrackingEntity;
use NAttreid\Tracking\Model\TrackingPages\TrackingPages;
use NAttreid\Tracking\Model\TrackingVisits\TrackingVisits;
use NAttreid\Utils\Range;
use Nette\Http\IRequest;
use Nette\SmartObject;
use Nette\Utils\DateTime;
use Nextras\Dbal\QueryException;
use Nextras\Dbal\Result\Result;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Model\Model;

/**
 * Tracking
 *
 * @author Attreid <attreid@gmail.com>
 */
class Tracking
{

	use SmartObject;

	/** @var int */
	private $minTimeBetweenVisits;

	/** @var Orm */
	private $orm;

	/** @var User */
	private $user;

	/** @var IRequest */
	private $request;

	public function __construct(int $minTimeBetweenVisits, Model $orm, User $user, IRequest $request)
	{
		$this->minTimeBetweenVisits = $minTimeBetweenVisits;
		$this->orm = $orm;
		$this->user = $user;
		$this->request = $request;
	}

	/**
	 * Vrati parametr
	 * @param string $name
	 * @param string $default
	 * @return string|null
	 */
	private function getParam(string $name, string $default = null): ?string
	{
		return $this->request->getPost($name, $default);
	}

	/**
	 * Vrati pocet online uzivatelu
	 * @return int
	 * @throws QueryException
	 */
	public function onlineUsers(): int
	{
		return $this->orm->tracking->onlineUsers();
	}

	/**
	 * Zaznam odchodu ze stranky
	 */
	public function leave(): void
	{
		$track = $this->orm->tracking->getLatest($this->user->getUid());

		if ($track) {
			$timeOnPage = time() - $track->inserted->getTimestamp();

			if ($timeOnPage < ($this->minTimeBetweenVisits * 60)) {
				$track->timeOnPage = $timeOnPage;
				$this->orm->persistAndFlush($track);
			}
		}
	}

	/**
	 * Zaznam prichodu na stranku
	 */
	public function track(): void
	{
		$track = new TrackingEntity;

		$track->uid = $this->user->getUid();
		$track->inserted = new DateTime;
		$track->url = $this->getParam('url');
		$track->referer = $this->getParam('referer');
		$track->ip = $this->request->getRemoteAddress();
		$track->browser = $this->getParam('browser');
		$track->utmSource = $this->getParam('utm_source');
		$track->utmMedium = $this->getParam('utm_medium');
		$track->utmCampaign = $this->getParam('utm_campaign');

		$this->orm->persistAndFlush($track);
	}

	/**
	 * Zaznam kliku
	 */
	public function clickTrack(): void
	{
		$uid = $this->user->getUid();
		$click = $this->getParam('click');

		$group = $this->orm->clickTrackingGroup->getByName($click);
		if (!$group) {
			$group = new ClickTrackingGroup;
			$group->name = $click;
			$this->orm->persistAndFlush($group);
		}

		$track = new ClickTracking;
		$track->uid = $uid;
		$track->inserted = new DateTime;
		$track->groupId = $group->id;
		$track->ip = $this->request->getRemoteAddress();
		$track->browser = $this->getParam('browser');
		$track->value = $this->getParam('value');
		$track->averageValue = $this->getParam('average');
		$track->sumValue = $this->getParam('sum');

		$this->orm->persistAndFlush($track);
	}

	/**
	 *
	 * @param Range $interval
	 * @return ICollection|TrackingPages[]
	 * @throws QueryException
	 */
	public function findPages(Range $interval): ICollection
	{
		foreach ($this->orm->trackingPages->findCalculateDate($interval) as $date) {
			if ($date !== null) {
				$date->setTime(0, 0, 0);
				$to = $date->modify('+23 hour');
				$to = $to->modify('+59 minute');
				$to = $to->modify('+59 second');

				$pages = $this->orm->tracking->findVisitPages(new Range($date, $to));
				foreach ($pages as $row) {
					$tp = $this->orm->trackingPages->getByKey($row->datefield, $row->page);
					if ($tp === null) {
						$tp = new TrackingPages;
					}
					$tp->datefield = $row->datefield;
					$tp->page = $row->page;
					$tp->visits = $row->visits;
					$tp->views = $row->views;

					$this->orm->persist($tp);
				}
			}
		}
		$this->orm->flush();

		$this->orm->refreshAll(true);
		return $this->orm->trackingPages->findPages($interval);
	}

	/**
	 * Pocet navstev po dnech
	 * @param Range $interval
	 * @return ICollection|TrackingVisits[]
	 * @throws QueryException
	 */
	public function findVisitsDays(Range $interval): ICollection
	{
		$this->checkVisits($interval);
		return $this->orm->trackingVisits->findVisitsDays($interval);
	}

	/**
	 * Pocet navstev po hodinach ve dni
	 * @param Range $interval
	 * @return Result|null
	 * @throws QueryException
	 */
	public function findVisitsHours(Range $interval): ?Result
	{
		$this->checkVisits($interval);
		return $this->orm->trackingVisits->findVisitsHours($interval);
	}

	/**
	 * Ulozeni zaznamu pokud jeste nejsou prepocitane
	 * @param Range $interval
	 * @throws QueryException
	 */
	private function checkVisits(Range $interval): void
	{
		foreach ($this->orm->trackingVisits->findCalculateDate($interval) as $date) {
			if ($date !== null) {
				$date = $date->setTime(0, 0, 0);
				$to = $date->modify('+23 hour');
				$to = $to->modify('+59 minute');
				$to = $to->modify('+59 second');

				$pages = $this->orm->tracking->findVisitsHours(new Range($date, $to), true);
				foreach ($pages as $row) {
					$trackingVisits = $this->orm->trackingVisits->getByKey(new \DateTime($row->datefield));
					if ($trackingVisits === null) {
						$trackingVisits = new TrackingVisits;
					}
					$trackingVisits->datefield = $row->datefield;
					$trackingVisits->visits = $row->visits;

					$this->orm->persistAndFlush($trackingVisits);
				}
			}
		}
		$this->orm->refreshAll(true);
	}

	/**
	 * Vrati pole skupin
	 * @return array
	 */
	public function fetchGroupPairs(): array
	{
		return $this->orm->clickTrackingGroup->fetchPairsByName();
	}

	/**
	 * Pocet kliku po dnech
	 * @param int $groupId
	 * @param Range $interval
	 * @return Result|null
	 */
	public function findClicksByDay(int $groupId, Range $interval): ?Result
	{
		return $this->orm->clickTracking->findClicksByDay($groupId, $interval);
	}

}
