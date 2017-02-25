<?php

/**
 * This file is part of the Cheatsheet package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (rex::isBackend() && rex::getUser()) {

    rex_extension::register('PAGES_PREPARED', function () {
        $providers = \rex_addon::get('cheatsheet')->getProperty('providers');
        foreach (\rex_package::getAvailablePackages() as $package) {
            if ($package->getProperty('cheatsheet')) {
                $property = $package->getProperty('cheatsheet');
                if (!is_array($property)) {
                    $property = [$property];
                }
                $providers = array_merge($providers, $property);
            }
        }

        if (count($providers) > 0) {
            $page = \rex_be_controller::getPageObject('cheatsheet');
            foreach ($providers as $provider) {
                $instance = new $provider();
                if (is_dir($instance->i18n())) {
                    \rex_i18n::addDirectory($instance->i18n());
                }
                $page->addSubPage($instance->page());
            }
        }
    });


    $stylesheets = $this->getProperty('stylesheets');

    if (count($stylesheets)) {
        foreach ($stylesheets as $stylesheet) {
            rex_view::addCssFile($this->getAssetsUrl($stylesheet));
        }
    }

    $javascripts = $this->getProperty('javascripts');

    if (count($javascripts)) {
        foreach ($javascripts as $javascript) {
            rex_view::addJsFile($this->getAssetsUrl($javascript));
        }
    }
}
