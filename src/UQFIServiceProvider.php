<?php

namespace Dlnsk\UQFI;

use Illuminate\Support\ServiceProvider;


class UQFIServiceProvider extends ServiceProvider
{
    /**
     * This will be used to register config & view in
     * package namespace.
     */
    protected $packageName = 'uqfi';

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        define("MOODLE_INTERNAL", "dummy");
        require_once(__DIR__ . '/../qformats/functions.php');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
