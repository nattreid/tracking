<?php

declare(strict_types=1);

namespace NAttreid\Tracking\Model\TrackingVisits;

use DateTimeImmutable;
use Nextras\Orm\Entity\Entity;

/**
 * TrackingVisits
 *
 * @property DateTimeImmutable $id {primary-proxy}
 * @property DateTimeImmutable $datefield {primary}
 * @property int $visits
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingVisits extends Entity
{

}
