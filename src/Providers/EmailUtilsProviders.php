<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2020-05-25
 * Time: 14:31
 */

namespace JoseChan\Email\Utils\Providers;


use Illuminate\Support\ServiceProvider;
use JoseChan\Email\Utils\Console\Commands\SendEmails;

class EmailUtilsProviders extends ServiceProvider
{
    public function register()
    {
        $this->commands(SendEmails::class);
    }
}
