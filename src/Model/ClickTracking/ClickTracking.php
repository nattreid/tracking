<?php

namespace NAttreid\Tracking\Model;

use Nextras\Dbal\Utils\DateTime;
use Nextras\Orm\Entity\Entity;

/**
 * ClickTracking
 *
 * @property int $id {primary-proxy}
 * @property string $uid {primary}
 * @property DateTime $inserted {primary}
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
