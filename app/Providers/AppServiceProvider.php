<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        config(['app.locale' => 'id']);
        Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');

        Validator::extend('equal', function ($attribute, $value, $parameters, $validator) {
            $min_field = $parameters[0];
            $data = $validator->getData();
            $min_value = $data[$min_field];
            return $value <= $min_value;
        });

        Validator::replacer('equal', function ($message, $attribute, $rule, $parameters) {
            return str_replace('_', ' ', 'kolom ' . $attribute . ' harus sama dengan ' . $parameters[0]);
        });
    }
}
