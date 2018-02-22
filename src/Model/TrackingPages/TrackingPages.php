<?php

declare(strict_types=1);

namespace NAttreid\Tracking\Model\TrackingPages;

use DateTimeImmutable;
use Nextras\Orm\Entity\Entity;

/**
 * TrackingPages
 *
 * @property mixed $id {primary-proxy}
 * @property DateTimeImmutable $datefield {primary}
 * @property string $page  {primary}
 * @property int $visits
 * @property int $views
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingPages extends Entity
{

}
