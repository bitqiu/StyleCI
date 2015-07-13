<?php

/*
 * This file is part of StyleCI.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\StyleCI\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * This is the http kernel class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var string[]
     */
    protected $middleware = [
        'Fideloper\Proxy\TrustProxies',
        'StyleCI\StyleCI\Http\Middleware\CheckForMaintenanceMode',
        'Illuminate\Cookie\Middleware\EncryptCookies',
        'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
        'Illuminate\Session\Middleware\StartSession',
        'Illuminate\View\Middleware\ShareErrorsFromSession',
    ];

    /**
     * The application's route middleware.
     *
     * @var string[]
     */
    protected $routeMiddleware = [
        'auth'  => 'StyleCI\StyleCI\Http\Middleware\Authenticate',
        'csrf'  => 'Illuminate\Foundation\Http\Middleware\VerifyCsrfToken',
        'guest' => 'StyleCI\StyleCI\Http\Middleware\RedirectIfAuthenticated',
    ];
}
