<?php

namespace AppBundle\Admin;

/**
 * Trait UserOwnableTrait
 * @package AppBundle\Admin
 */
trait UserOwnableTrait
{
    /**
     * @return null
     */
    public function getClient(){
        $result = null;
        try {
            $result = $this->getParent()->getSubject();
        } catch (\Exception $exception) {

        } finally {
            return $result;
        }
    }
}
