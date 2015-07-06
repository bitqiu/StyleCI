<?php

/*
 * This file is part of StyleCI.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\StyleCI\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use StyleCI\StyleCI\Commands\DeleteAccountCommand;
use StyleCI\StyleCI\Commands\DisableRepoCommand;
use StyleCI\StyleCI\Commands\EnableRepoCommand;
use StyleCI\StyleCI\GitHub\Repos;
use StyleCI\StyleCI\Models\Repo;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * This is the account controller class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class AccountController extends AbstractController
{
    /**
     * The authentication guard instance.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * The github repos instance.
     *
     * @var \StyleCI\StyleCI\GitHub\Repos
     */
    protected $repos;

    /**
     * Create a new account controller instance.
     *
     * @param \Illuminate\Contracts\Auth\Guard $auth
     * @param \StyleCI\StyleCI\GitHub\Repos    $repos
     *
     * @return void
     */
    public function __construct(Guard $auth, Repos $repos)
    {
        parent::__construct();

        $this->auth = $auth;
        $this->repos = $repos;

        $this->middleware('auth');
    }

    /**
     * Show the user's account information.
     *
     * @return \Illuminate\View\View
     */
    public function handleShow()
    {
        return View::make('account');
    }

    /**
     * Show the user's public repositories.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function handleListRepos(Request $request)
    {
        $repos = $this->repos->get($this->auth->user(), true);

        if ($request->ajax()) {
            return new JsonResponse(['data' => $repos]);
        }

        return View::make('account')->withRepos($repos);
    }

    /**
     * Sync the user's public repositories.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function handleSync(Request $request)
    {
        $this->repos->flush($this->auth->user());

        if ($request->ajax()) {
            return new JsonResponse(['flushed' => true]);
        }

        return Redirect::route('account_path');
    }

    /**
     * Enable StyleCI for a repo.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function handleEnable(Request $request, $id)
    {
        $repo = array_get($this->repos->get($this->auth->user(), true), $id);

        if (!$repo) {
            throw new HttpException(403);
        }

        $this->dispatch(new EnableRepoCommand($id, $repo['name'], $this->auth->user()));

        if ($request->ajax()) {
            return new JsonResponse(['enabled' => true]);
        }

        return Redirect::route('account_path');
    }

    /**
     * Disable StyleCI for a repo.
     *
     * @param \Illuminate\Http\Request     $request
     * @param \StyleCI\StyleCI\Models\Repo $repo
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function handleDisable(Request $request, Repo $repo)
    {
        if (!array_get($this->repos->get($this->auth->user(), true), $repo->id)) {
            throw new HttpException(403);
        }

        $this->dispatch(new DisableRepoCommand($repo));

        if ($request->ajax()) {
            return new JsonResponse(['enabled' => false]);
        }

        return Redirect::route('account_path');
    }

    /**
     * Delete the user's account and all their repos.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleDelete()
    {
        $this->dispatch(new DeleteAccountCommand($this->auth->user()));

        return Redirect::route('home')->with('success', 'Your account has been successfully deleted!');
    }
}
