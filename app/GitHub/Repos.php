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

use Github\ResultPager;
use Illuminate\Contracts\Cache\Repository;
use StyleCI\StyleCI\Models\Repo;
use StyleCI\StyleCI\Models\User;

/**
 * This is the github repos class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class Repos
{
    /**
     * The github client factory instance.
     *
     * @var \StyleCI\StyleCI\GitHub\ClientFactory
     */
    protected $factory;

    /**
     * The illuminate cache repository instance.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Create a github repos instance.
     *
     * @param \StyleCI\StyleCI\GitHub\ClientFactory  $factory
     * @param \Illuminate\Contracts\Cache\Repository $cache
     *
     * @return void
     */
    public function __construct(ClientFactory $factory, Repository $cache)
    {
        $this->factory = $factory;
        $this->cache = $cache;
    }

    /**
     * Get a user's public repos.
     *
     * @param \StyleCI\StyleCI\Models\User $user
     * @param bool                         $admin
     *
     * @return array
     */
    public function get(User $user, $admin = false)
    {
        // cache the repo info from github for 12 hours
        $list = $this->cache->remember($user->id.'repos', 720, function () use ($user) {
            return $this->fetchFromGitHub($user);
        });

        foreach (Repo::whereIn('id', array_keys($list))->get(['id', 'name', 'default_branch']) as $repo) {
            $list[$repo->id]['enabled'] = true;
            $this->syncWithDatabase($repo, $list[$repo->id]);
        }

        if ($admin) {
            $list = array_filter($list, function ($item) {
                return $item['admin'];
            });
        }

        return $list;
    }

    /**
     * Fetch a user's public repos from github.
     *
     * @param \StyleCI\StyleCI\Models\User $user
     *
     * @return array
     */
    protected function fetchFromGitHub(User $user)
    {
        $client = $this->factory->make($user);
        $paginator = new ResultPager($client);

        $list = [];

        foreach ($paginator->fetchAll($client->me(), 'repositories', ['public']) as $repo) {
            // set enabled to false by default
            // we'll mark those that are enabled at a later point
            $list[$repo['id']] = ['name' => $repo['full_name'], 'default_branch' => $repo['default_branch'], 'language' => $repo['language'], 'admin' => $repo['permissions']['admin'], 'enabled' => false];
        }

        return $list;
    }

    /**
     * Save all downloaded changes to the database for the given repo.
     *
     * @param \StyleCI\StyleCI\Models\Repo $repo
     * @param array                        $data
     *
     * @return void
     */
    protected function syncWithDatabase(Repo $repo, array $data)
    {
        $modifed = false;

        if ($repo->name !== $data['name']) {
            $repo->name = $data['name'];
            $modifed = true;
        }

        if ($repo->default_branch !== $data['default_branch']) {
            $repo->default_branch = $data['default_branch'];
            $modifed = true;
        }

        if ($modifed) {
            $repo->save();
        }
    }

    /**
     * Flush our cache of the user's public repos.
     *
     * @param \StyleCI\StyleCI\Models\User $user
     *
     * @return void
     */
    public function flush(User $user)
    {
        $this->cache->forget($user->id.'repos');
    }
}
