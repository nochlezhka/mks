<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Admin;

use App\Entity\Client;

trait UserOwnableTrait
{
    public function getClient(): ?Client {
        $result = null;

        try {
            $result = $this->getParent()->getSubject();
        } catch (\Throwable) {
        } finally {
            return $result;
        }
    }
}
