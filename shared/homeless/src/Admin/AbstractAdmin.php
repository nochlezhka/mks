<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as SonataAbstractAdmin;

abstract class AbstractAdmin extends SonataAbstractAdmin
{
    use AdminTrait;
}
