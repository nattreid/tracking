<?php

namespace NAttreid\Tracking\Model\TrackingPages;

use Nextras\Dbal\Utils\DateTime;
use Nextras\Orm\Entity\Entity;

/**
 * TrackingPages
 *
 * @property mixed $id {primary-proxy}
 * @property DateTime $datefield {primary}
 * @property string $page  {primary}
 * @property int $visits
 * @property int $views
 *
 * @author Attreid <attreid@gmail.com>
 */
class TrackingPages extends Entity
{

}
