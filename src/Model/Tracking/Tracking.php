<?php

declare(strict_types=1);

namespace NAttreid\Tracking\Model\Tracking;

use DateTimeImmutable;
use Nextras\Orm\Entity\Entity;

/**
 * Tracking
 *
 * @property mixed $id {primary-proxy}
 * @property int $aiid {primary}
 * @property string $uid
 * @property DateTimeImmutable $inserted {primary}
 * @property string $url
 * @property string $referer
 * @property string|null $ip
 * @property string|null $browser
 * @property int|null $timeOnPage
 * @property string|null $utmSource
 * @property string|null $utmMedium
 * @property string|null $utmCampaign
 *
 * @author Attreid <attreid@gmail.com>
 */
class Tracking extends Entity
{
	public function setTimeOnPage(int $timeOnPage): void
	{
		/* @var $repository TrackingRepository */
		$repository = $this->getRepository();
		$repository->updateTimeOnPage($this->aiid, $timeOnPage);
	}
}
