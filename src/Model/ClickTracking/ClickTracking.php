<?php

namespace NAttreid\Tracking\Model;

use Nextras\Dbal\Utils\DateTime;

/**
 * ClickTracking
 * 
 * @property int $id {primary-proxy}
 * @property string $uid {primary}
 * @property DateTime $inserted {primary}
 * @property int $groupId
 * @property string|NULL $ip
 * @property string|NULL $browser
 * @property string|NULL $value
 * @property float|NULL $averageValue
 * @property float|NULL $sumValue
 * 
 * @author Attreid <attreid@gmail.com>
 */
class ClickTracking extends \Nextras\Orm\Entity\Entity {
    
}
