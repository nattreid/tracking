<?php

declare(strict_types=1);

namespace NAttreid\Tracking\Model\ClickTracking;

use DateTimeImmutable;
use Nextras\Orm\Entity\Entity;

/**
 * ClickTracking
 *
 * @property mixed $id {primary-proxy}
 * @property int $aiid {primary}
 * @property string $uid
 * @property DateTimeImmutable $inserted {primary}
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
