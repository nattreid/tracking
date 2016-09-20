<?php

namespace NAttreid\Tracking\Model;

use Nette\Utils\DateTime;
use Nextras\Orm\Entity\Entity;

/**
 * Tracking
 *
 * @property mixed $id {primary-proxy}
 * @property string $uid {primary}
 * @property DateTime $inserted {primary}
 * @property string $url
 * @property string $referer
 * @property string|NULL $ip
 * @property string|NULL $browser
 * @property int|NULL $timeOnPage
 * @property string|NULL $utmSource
 * @property string|NULL $utmMedium
 * @property string|NULL $utmCampaign
 *
 * @author Attreid <attreid@gmail.com>
 */
class Tracking extends Entity
{

}
