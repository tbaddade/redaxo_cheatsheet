<?php

/**
 * This file is part of the Cheatsheet package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$addon = rex_addon::get('cheatsheet');

if (rex::isBackend() && is_object(rex::getUser())) {
    rex_extension::register('PAGES_PREPARED', function () use ($addon) {
        $providers = $addon->getProperty('providers', []);
        foreach (\rex_package::getAvailablePackages() as $package) {
            if ($package->hasProperty('cheatsheet')) {
                $property = $package->getProperty('cheatsheet');
                if (!is_array($property)) {
                    $property = [$property];
                }
                $providers = array_merge($providers, $property);
            }
        }

        $page = \rex_be_controller::getPageObject('cheatsheet');
        foreach ($providers as $provider) {
            $instance = new $provider();
            if (is_dir($instance->i18n())) {
                \rex_i18n::addDirectory($instance->i18n());
            }
            $page->addSubpage($instance->page());
        }
    });


    $stylesheets = $addon->getProperty('stylesheets', []);
    foreach ($stylesheets as $stylesheet) {
        rex_view::addCssFile($addon->getAssetsUrl($stylesheet));
    }

    $javascripts = $addon->getProperty('javascripts', []);
    foreach ($javascripts as $javascript) {
        rex_view::addJsFile($addon->getAssetsUrl($javascript));
    }
}
