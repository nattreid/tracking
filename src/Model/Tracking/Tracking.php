<?php

namespace NAttreid\Tracking\Model;

use Nette\Utils\DateTime;
use Nextras\Orm\Entity\Entity;

/**
 * Tracking
 *
 * @property mixed $id {primary}
 * @property string $uid
 * @property DateTime $inserted
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

}
