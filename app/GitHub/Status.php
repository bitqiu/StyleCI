<?php

/*
 * This file is part of StyleCI.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\StyleCI\GitHub;

use StyleCI\StyleCI\Models\Commit;

/**
 * This is the github status class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class Status
{
    /**
     * The github client factory instance.
     *
     * @var \StyleCI\StyleCI\GitHub\ClientFactory
     */
    protected $factory;

    /**
     * The target url.
     *
     * @var string
     */
    protected $url;

    /**
     * Create a new github status instance.
     *
     * @param \StyleCI\StyleCI\GitHub\ClientFactory $factory
     * @param string                                $url
     *
     * @return void
     */
    public function __construct(ClientFactory $factory, $url)
    {
        $this->factory = $factory;
        $this->url = $url;
    }

    /**
     * Push the status on the github commit.
     *
     * @param \StyleCI\StyleCI\Models\Commit $commit
     *
     * @return void
     */
    public function push(Commit $commit)
    {
        $repo = $commit->repo;

        $args = explode('/', $repo->name);

        $data = [
            'state'       => $this->getState($commit->status),
            'description' => $commit->description(),
            'target_url'  => $this->url.'/'.$commit->id,
            'context'     => 'StyleCI',
        ];

        $client = $this->factory->make($repo);

        $client->repos()->statuses()->create($args[0], $args[1], $commit->id, $data);
    }

    /**
     * Get the state of the commit by from its status integer.
     *
     * @param int $status
     *
     * @return string
     */
    protected function getState($status)
    {
        switch ($status) {
            case 1:
                return 'success';
            case 2:
                return 'failure';
            case 3:
            case 4:
                return 'error';
            default:
                return 'pending';
        }
    }
}
