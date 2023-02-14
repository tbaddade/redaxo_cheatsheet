<?php

/**
 * This file is part of the Cheatsheet package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cheatsheet;

use rex_be_page;

interface PageInterface
{

    /**
     * Adds a subpage
     *
     * @param rex_be_page $page
     */
    public function addSubpage(rex_be_page $page);

    /**
     * Returns the backend page.
     *
     * @return rex_be_page
     */
    public function get(): rex_be_page;

    /**
     * Returns the href of the page.
     *
     * @return string href
     */
    public function getHref(): string;

    /**
     * Sets the href.
     *
     * @param array|string $value
     */
    public function setHref($value);

    /**
     * Returns the key of the page.
     *
     * @return string key
     */
    public function getKey(): string;

    /**
     * Sets the key.
     *
     * @param string $value
     */
    public function setKey(string $value);

    /**
     * Returns the path of the page.
     *
     * @return string Path
     */
    public function getPath(): string;

    /**
     * Sets the path.
     *
     * @param string $value
     */
    public function setPath(string $value);

    /**
     * Returns the title of the page.
     *
     * @return string Title
     */
    public function getTitle(): string;

    /**
     * Sets the title.
     *
     * @param string $value
     */
    public function setTitle(string $value);

}
