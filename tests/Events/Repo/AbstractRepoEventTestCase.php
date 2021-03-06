<?php

/*
 * This file is part of StyleCI.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\Tests\StyleCI\Events\Repo;

use StyleCI\StyleCI\Events\Repo\RepoEventInterface;
use StyleCI\Tests\StyleCI\Events\AbstractEventTestCase;

/**
 * This is the abstract repo event test case class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class AbstractRepoEventTestCase extends AbstractEventTestCase
{
    protected function getEventInterfaces()
    {
        return [RepoEventInterface::class];
    }
}
