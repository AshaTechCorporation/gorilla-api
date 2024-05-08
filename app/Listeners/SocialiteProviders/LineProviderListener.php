<?php

namespace App\Listeners\SocialiteProviders;

use Laravel\Socialite\SocialiteManager;
use SocialiteProviders\Line\Provider;

class LineProviderListener
{
    public function handle(SocialiteManager $socialmedia)
    {
        $socialmedia->extend(
            'line',
            function ($app) use ($socialmedia) {
                $config = $app['config']['services.line'];
                return $socialmedia->buildProvider(Provider::class, $config);
            }
        );
    }
}