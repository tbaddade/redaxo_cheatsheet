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
