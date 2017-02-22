<?php

/**
 * This file is part of the Cheatsheet package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cheatsheet\Support;


abstract class ServiceProvider
{

    /**
     * Register the directory to search a translation file.
     *
     * @return string
     */
    abstract function i18n();


    /**
     * Register the page provider.
     *
     * @return Page
     */
    abstract function page();
}
