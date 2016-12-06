<?php

namespace NAttreid\Tracking\Model;

use Nextras\Dbal\Utils\DateTime;
use Nextras\Orm\Entity\Entity;

/**
 * ClickTracking
 *
 * @property mixed $id {primary-proxy}
 * @property int $aiid {primary}
 * @property string $uid
 * @property DateTime $inserted  {primary}
 * @property int $groupId
 * @property string|null $ip
 * @property string|null $browser
 * @property string|null $value
 * @property float|null $averageValue
 * @property float|null $sumValue
 *
 * @author Attreid <attreid@gmail.com>
 */
class ClickTracking extends Entity
{

}
