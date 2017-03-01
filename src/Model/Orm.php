<?php

declare(strict_types = 1);

namespace NAttreid\Tracking\Model;

use NAttreid\Tracking\Model\ClickTracking\ClickTrackingRepository;
use NAttreid\Tracking\Model\ClickTrackingGroup\ClickTrackingGroupRepository;
use NAttreid\Tracking\Model\Tracking\TrackingRepository;
use NAttreid\Tracking\Model\TrackingPages\TrackingPagesRepository;
use NAttreid\Tracking\Model\TrackingVisits\TrackingVisitsRepository;
use Nextras\Orm\Model\Model;

/**
 * @property-read TrackingRepository $tracking
 * @property-read TrackingPagesRepository $trackingPages
 * @property-read TrackingVisitsRepository $trackingVisits
 *
 * @property-read ClickTrackingRepository $clickTracking
 * @property-read ClickTrackingGroupRepository $clickTrackingGroup
 *
 * @author Attreid <attreid@gmail.com>
 */
class Orm extends Model
{

}
