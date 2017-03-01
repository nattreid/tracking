<?php

declare(strict_types = 1);

namespace NAttreid\Tracking\Model\TrackingVisits;

use Nextras\Dbal\Utils\DateTime;
use Nextras\Orm\Entity\Entity;

/**
 * TrackingVisits
 *
 * @property int $id {primary-proxy}
 * @property DateTime $datefield {primary}
 * @property int $visits
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingVisits extends Entity
{

}
