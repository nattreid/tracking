<?php

namespace NAttreid\Tracking\DI;

use NAttreid\Routing\RouterFactory;
use NAttreid\Tracking\Model\TrackingMapper;
use NAttreid\Tracking\Routing\Router;
use NAttreid\Tracking\Tracking;

/**
 * Tracking rozsireni
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingExtension extends \Nette\DI\CompilerExtension
{

	private $defaults = [
		'trackUrl' => 'track',
		'clickUrl' => 'clickTrack',
		'minTimeBetweenVisits' => 30,
		'onlineTime' => 3
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->getConfig());

		$builder->addDefinition($this->prefix('tracking'))
			->setClass(Tracking::class)
			->setArguments([$config['minTimeBetweenVisits']]);

		$builder->addDefinition($this->prefix('router'))
			->setClass(Router::class)
			->setArguments([$config['trackUrl'], $config['clickUrl']]);
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->getConfig());

		$router = $builder->getByType(RouterFactory::class);
		try {
			$builder->getDefinition($router)
				->addSetup('addRouter', ['@' . $this->prefix('router'), RouterFactory::PRIORITY_SYSTEM]);
		} catch (\Nette\DI\MissingServiceException $ex) {
			throw new \Nette\DI\MissingServiceException("Missing extension 'nattreid/routing'");
		}

		$builder->getDefinition('application.presenterFactory')
			->addSetup('setMapping', [
				['TrackingExt' => 'NAttreid\Tracking\Control\*Presenter']
			]);
		try {
			$trackingMapper = $builder->getByType(TrackingMapper::class);
			$builder->getDefinition($trackingMapper)
				->addSetup('setup', [$config['minTimeBetweenVisits'], $config['onlineTime']]);
		} catch (\Nette\DI\MissingServiceException $ex) {
			throw new \Nette\DI\MissingServiceException("'NAttreid\Tracking\Model\Orm' is not added to orm.");
		}
	}

}
