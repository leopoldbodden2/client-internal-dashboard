<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        /**
         * Validates a phone number
         */
        Validator::extend('phone', function($attribute, $value, $parameters, $validator) {
            if(is_array($value)){
                foreach ($value as $val) {
                    $validator = (is_null($val)?true:preg_match('%^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\/]?){0,})(?:[\-\.\ \\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\/]?(\d+))?$%i', $val) && strlen($val) >= 10);
                    if(!$validator){
                        break;
                    }
                }
                return true;
            }
            else{
                return is_null($value)?true:preg_match('%^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\/]?){0,})(?:[\-\.\ \\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\/]?(\d+))?$%i', $value) && strlen($value) >= 10;
            }

            return false;
        });

        Validator::replacer('phone', function($message, $attribute, $rule, $parameters) {
            return 'The phone you entered is invalid.';//str_replace(':attribute',ucfirst($attribute),':attribute is an invalid phone number');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
