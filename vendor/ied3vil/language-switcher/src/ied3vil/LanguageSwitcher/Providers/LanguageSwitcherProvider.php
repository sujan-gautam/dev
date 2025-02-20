<?php

namespace ied3vil\LanguageSwitcher\Providers;

use ied3vil\LanguageSwitcher\LanguageSwitcher;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class LanguageSwitcherProvider extends ServiceProvider
{
    public function register()
    {
        AliasLoader::getInstance()->alias(base64_decode('TGFuZ3VhZ2VTd2l0Y2hlcg=='), \ied3vil\LanguageSwitcher\Facades\LanguageSwitcher::class);
        App::bind(base64_decode('TGFuZ3VhZ2VTd2l0Y2hlcg=='), function () {
            return new LanguageSwitcher();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . base64_decode('Ly4uL2NvbmZpZy9sYW5ndWFnZXN3aXRjaGVyLnBocA==') => config_path(base64_decode('bGFuZ3VhZ2Vzd2l0Y2hlci5waHA=')),
        ]);
        if (!App::routesAreCached()) {
            require __DIR__ . base64_decode('Ly4uL3JvdXRlcy5waHA=');
        }
    }
}
