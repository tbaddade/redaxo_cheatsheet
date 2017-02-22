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

interface PageInterface
{

    /**
     * Adds a subpage
     *
     * @param \rex_be_page $page
     */
    public function addSubpage(\rex_be_page $page);

    /**
     * Returns the backend page.
     *
     * @return \rex_be_page
     */
    public function get();

    /**
     * Returns the href of the page.
     *
     * @return string Href
     */
    public function getHref();

    /**
     * Returns the key of the page.
     *
     * @return string Key
     */
    public function getKey();

    /**
     * Sets the key.
     *
     * @param string $value
     */
    public function setKey($value);

    /**
     * Returns the path of the page.
     *
     * @return string Path
     */
    public function getPath();

    /**
     * Sets the path.
     *
     * @param string $value
     */
    public function setPath($value);

    /**
     * Returns the title of the page.
     *
     * @return string Title
     */
    public function getTitle();

    /**
     * Sets the title.
     *
     * @param string $value
     */
    public function setTitle($value);

}
