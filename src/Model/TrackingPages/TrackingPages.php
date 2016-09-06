<?php

namespace NAttreid\Tracking\Model;

use Nextras\Dbal\Utils\DateTime;

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
class TrackingPages extends \Nextras\Orm\Entity\Entity
{

}
