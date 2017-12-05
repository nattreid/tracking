<?php

declare(strict_types=1);

namespace NAttreid\Tracking\Control;

use NAttreid\Tracking\Tracking;
use Nette\Application\AbortException;
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
	private function getPost(string $name): ?string
	{
		return $this->getHttpRequest()->getPost($name);
	}

	/**
	 * Trackovani navstev
	 * @throws AbortException
	 */
	public function actionTrack(): void
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
	 * @throws AbortException
	 */
	public function actionClickTrack(): void
	{
		if ($this->getPost('click')) {
			$this->tracking->clickTrack();
		}
		$this->terminate();
	}

}
