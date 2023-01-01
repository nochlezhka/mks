<?php

namespace App\Admin;

/**
 * Trait UserOwnableTrait
 * @package App\Admin
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
