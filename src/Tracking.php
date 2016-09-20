<?php

namespace NAttreid\Tracking;

use NAttreid\Security\User;
use NAttreid\Tracking\Model\ClickTracking;
use NAttreid\Tracking\Model\ClickTrackingGroup;
use NAttreid\Tracking\Model\Orm;
use NAttreid\Tracking\Model\TrackingPages;
use NAttreid\Tracking\Model\TrackingVisits;
use NAttreid\Utils\Range;
use Nette\Http\IRequest;
use Nette\SmartObject;
use Nette\Utils\DateTime;
use Nextras\Dbal\Result\Result;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Model\Model;
use NAttreid\Tracking\Model\Tracking as TrackingEntity;

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

	public function __construct($minTimeBetweenVisits, Model $orm, User $user, IRequest $request)
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
	 * @return string
	 */
	private function getParam($name, $default = NULL)
	{
		return $this->request->getPost($name, $default);
	}

	/**
	 * Vrati pocet online uzivatelu
	 * @return int
	 */
	public function onlineUsers()
	{
		return $this->orm->tracking->onlineUsers();
	}

	/**
	 * Zaznam odchodu ze stranky
	 */
	public function leave()
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
	public function track()
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
	public function clickTrack()
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
	 */
	public function findPages(Range $interval)
	{
		foreach ($this->orm->trackingPages->findCalculateDate($interval) as $date) {
			if ($date !== NULL) {
				$date->setTime(0, 0, 0);
				$to = clone $date;
				$to->modify('+23 HOUR');
				$to->modify('+59 MINUTE');
				$to->modify('+59 SECONDS');

				$pages = $this->orm->tracking->findVisitPages(new Range($date, $to));
				foreach ($pages as $row) {
					$tp = $this->orm->trackingPages->getByKey($row->datefield, $row->page);
					if ($tp === NULL) {
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

		return $this->orm->trackingPages->findPages($interval);
	}

	/**
	 * Pocet navstev po dnech
	 * @param Range $interval
	 * @return ICollection|TrackingVisits[]
	 */
	public function findVisitsDays(Range $interval)
	{
		$this->checkVisits($interval);
		return $this->orm->trackingVisits->findVisitsDays($interval);
	}

	/**
	 * Pocet navstev po hodinach ve dni
	 * @param Range $interval
	 * @return Result
	 */
	public function findVisitsHours(Range $interval)
	{
		$this->checkVisits($interval);
		return $this->orm->trackingVisits->findVisitsHours($interval);
	}

	/**
	 * Ulozeni zanzamu pokud jeste nejsou prepocitane
	 * @param Range $interval
	 */
	private function checkVisits(Range $interval)
	{
		foreach ($this->orm->trackingVisits->findCalculateDate($interval) as $date) {
			if ($date !== NULL) {
				$date->setTime(0, 0, 0);
				$to = clone $date;
				$to->modify('+23 HOUR');
				$to->modify('+59 MINUTE');
				$to->modify('+59 SECONDS');

				$pages = $this->orm->tracking->findVisitsHours(new Range($date, $to), TRUE);
				foreach ($pages as $row) {
					$tp = $this->orm->trackingVisits->getByKey(new \DateTime($row->datefield));
					if ($tp === NULL) {
						$tp = new TrackingVisits;
					}
					$tp->datefield = $row->datefield;
					$tp->visits = $row->visits;

					$this->orm->persistAndFlush($tp);
				}
			}
		}
	}

	/**
	 * Vrati pole skupin
	 * @return array
	 */
	public function fetchGroupPairs()
	{
		return $this->orm->clickTrackingGroup->fetchPairsByName();
	}

	/**
	 * Pocet kliku po dnech
	 * @param int $groupId
	 * @param Range $interval
	 * @return Result
	 */
	public function findClicksByDay($groupId, Range $interval)
	{
		return $this->orm->clickTracking->findClicksByDay($groupId, $interval);
	}

}
