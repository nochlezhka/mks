<?php

namespace App\Admin;

use App\Entity\Client;

/**
 * Trait UserOwnableTrait
 * @package App\Admin
 */
trait UserOwnableTrait
{
    public function getClient(): ?Client{
        $result = null;
        try {
            $result = $this->getParent()->getSubject();
        } catch (\Exception $exception) {

        } finally {
            return $result;
        }
    }
}
