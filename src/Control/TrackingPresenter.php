<?php

namespace NAttreid\Tracking\Control;

use NAttreid\Tracking\Tracking;

/**
 * Presenter por tracking
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingPresenter extends \Nette\Application\UI\Presenter
{

	/** @var Tracking */
	private $tracking;

	public function __construct(Tracking $tracking)
	{
		parent::__construct();
		$this->tracking = $tracking;
	}

	/**
	 * Vrati hodnotu promenne z POST, pokud neni vrati NULL
	 * @param string $name
	 * @return string
	 */
	private function getPost($name)
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
	public function actionclickTrack()
	{
		if ($this->getPost('click')) {
			$this->tracking->clickTrack();
		}
		$this->terminate();
	}

}
