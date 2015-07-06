<?php

/*
 * This file is part of StyleCI.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\StyleCI\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;

/**
 * This is the repo routes class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class RepoRoutes
{
    /**
     * Define the repo routes.
     *
     * @param \Illuminate\Contracts\Routing\Registrar $router
     *
     * @return void
     */
    public function map(Registrar $router)
    {
        $router->get('repos', [
            'as'   => 'repos_path',
            'uses' => 'RepoController@handleList',
        ]);

        $router->get('repos/{repo}', [
            'as'   => 'repo_path',
            'uses' => 'RepoController@handleShow',
        ]);

        $router->get('repos/{repo}/shield', [
            'as'   => 'repo_shield_path',
            'uses' => 'ShieldController@handle',
        ]);

        $router->post('repos/{repo}/analyse', [
            'as'   => 'repo_analyse_path',
            'uses' => 'RepoController@handleAnalyse',
        ]);

        $router->get('commits/{commit}', [
            'as'   => 'commit_path',
            'uses' => 'CommitController@handleShow',
        ]);

        $router->get('commits/{commit}/diff', [
            'as'   => 'commit_diff_path',
            'uses' => 'CommitController@handleDiff',
        ]);

        $router->get('commits/{commit}/diff/download', [
            'as'   => 'commit_download_path',
            'uses' => 'CommitController@handleDiffDownload',
        ]);
    }
}
