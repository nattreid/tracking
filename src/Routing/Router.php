<?php

namespace NAttreid\Tracking\Routing;

use Nette\Application\Routers\Route;

/**
 * Deploy router
 *
 * @author Attreid <attreid@gmail.com>
 */
class Router extends \NAttreid\Routing\Router
{

	/** @var string */
	private $trackUrl;

	/** @var string */
	private $clickUrl;

	public function __construct($trackUrl, $clickUrl)
	{
		parent::__construct();
		$this->trackUrl = $trackUrl . '/';
		$this->clickUrl = $clickUrl . '/';
	}

	public function createRoutes()
	{
		$router = $this->getRouter();

		$router[] = new Route($this->trackUrl, 'Tracking:Tracking:track');
		$router[] = new Route($this->clickUrl, 'Tracking:Tracking:clickTrack');
	}

}
