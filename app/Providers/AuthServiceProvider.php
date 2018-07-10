<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
//  will register the routes necessary to issue access tokens and revoke access tokens, clients, and personal access tokens
         Passport::routes();

         // Passport::tokensExpireIn(Carbon::now()->addMinutes(10));

         Passport::tokensExpireIn(Carbon::now()->addMinutes(10));

         Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
    }
}
