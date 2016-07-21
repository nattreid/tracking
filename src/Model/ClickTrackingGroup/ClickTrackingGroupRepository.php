<?php

namespace NAttreid\Tracking\Model;

/**
 * ClickTrackingGroup Repository
 *
 * @author Attreid <attreid@gmail.com>
 */
class ClickTrackingGroupRepository extends \NAttreid\Orm\Repository {

    public static function getEntityClassNames() {
        return [ClickTrackingGroup::class];
    }

    /**
     * Vrati radek podle jmena
     * @param string $name
     * @return ClickTrackingGroup
     */
    public function getByName($name) {
        return $this->findBy(['name' => $name])->fetch();
    }

}
