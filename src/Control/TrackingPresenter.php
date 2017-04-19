<?php

declare(strict_types=1);

namespace NAttreid\Tracking\Control;

use NAttreid\Tracking\Tracking;
use Nette\Application\UI\Presenter;

/**
 * Presenter por tracking
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingPresenter extends Presenter
{

	/** @var Tracking */
	private $tracking;

	public function __construct(Tracking $tracking)
	{
		parent::__construct();
		$this->tracking = $tracking;
	}

	/**
	 * Vrati hodnotu promenne z POST, pokud neni vrati null
	 * @param string $name
	 * @return string|null
	 */
	private function getPost(string $name)
	{
		return $this->getHttpRequest()->getPost($name);
	}

	/**
	 * Trackovani navstev
	 */
	public function actionTrack()
	{
		if ($this->getPost('leave')) {
			$this->tracking->leave();
		}
		if ($this->getPost('url')) {
			$this->tracking->track();
		}
		$this->terminate();
	}

	/**
	 * Trackovani kliku
	 */
	public function actionClickTrack()
	{
		if ($this->getPost('click')) {
			$this->tracking->clickTrack();
		}
		$this->terminate();
	}

}
