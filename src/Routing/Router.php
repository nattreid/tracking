<?php

namespace NAttreid\Tracking\Routing;

use Nette\Application\Routers\Route;

/**
 * Deploy router
 *
 * @author Attreid <attreid@gmail.com>
 */
class Router extends \NAttreid\Routing\Router {

    /** @var string */
    private $trackUrl;

    /** @var string */
    private $clickUrl;

    public function __construct($trackUrl, $clickUrl) {
        $this->trackUrl = $trackUrl . '/';
        $this->clickUrl = $clickUrl . '/';
    }

    public function createRoutes() {
        $router = $this->getRouter();

        $router[] = new Route($this->trackUrl, 'TrackingExt:Tracking:track');
        $router[] = new Route($this->clickUrl, 'TrackingExt:Tracking:clickTrack');
    }

}
