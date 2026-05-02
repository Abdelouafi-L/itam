<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * This is where we customize Laravel's auth system to work
     * with our split Employee/User schema.
     */
    public function boot(): void
    {
        // Register a custom auth provider that finds users by
        // email on the employees table instead of users table.
        //
        // Raw PHP equivalent: your manual PDO query in Auth.php:
        // SELECT u.* FROM users u
        // JOIN employees e ON e.id = u.employee_id
        // WHERE e.email = ?
        Auth::provider('employee_email', function ($app, array $config) {
            return new class($app['hash'], $config['model'])
                extends EloquentUserProvider {

                /**
                 * Override the default "find by email" query.
                 * Called by Auth::attempt(['email' => ...])
                 */
                public function retrieveByCredentials(array $credentials
                ): Authenticatable|null {
                    if (empty($credentials['email'])) {
                        return null;
                    }

                    // Find user by joining through employee email
                    return $this->createModel()
                        ->whereHas('employee', function ($query) use (
                            $credentials
                        ) {
                            $query->where(
                                'email',
                                $credentials['email']
                            );
                        })
                        ->where('is_active', true)
                        ->first();
                }
            };
        });
    }
}