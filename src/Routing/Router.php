<?php

declare(strict_types=1);

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

	public function __construct(string $trackUrl, string $clickUrl)
	{
		parent::__construct();
		$this->trackUrl = $trackUrl . '/';
		$this->clickUrl = $clickUrl . '/';
	}

	public function createRoutes(): void
	{
		$router = $this->getRouter();

		$router[] = new Route($this->trackUrl, 'Tracking:Tracking:track');
		$router[] = new Route($this->clickUrl, 'Tracking:Tracking:clickTrack');
	}

}
