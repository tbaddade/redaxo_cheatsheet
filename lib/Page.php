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
use rex_path;

class Page implements PageInterface
{
    const ADDON = 'cheatsheet';

    private string $href;
    private string $key;
    private string $path;
    private string $title;
    private array $subpages = [];

    public function __construct()
    {
        $this->subpages = [];
    }


    /**
     * {@inheritdoc}
     */
    public function addSubpage(rex_be_page $page)
    {
        $this->subpages[] = $page;
    }


    /**
     * {@inheritdoc}
     */
    public function get(): rex_be_page
    {
        $page = new rex_be_page($this->getKey(), $this->getTitle());
        $page->setSubPath($this->getPath());
        if ('' !== $this->getHref()) {
            $page->setHref($this->getHref());
        } else {
            $page->setHref('index.php?page=' . self::ADDON . '/' . $this->key);
        }

        if (count($this->subpages) > 0) {
            foreach ($this->subpages as $subpage) {
                $subpage->setHref('index.php?page=' . self::ADDON . '/' . $this->key . '/' . $subpage->getKey());
                $page->addSubpage($subpage);
            }
        }

        return $page;
    }

    /**
     * {@inheritdoc}
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * {@inheritdoc}
     */
    public function setHref($value)
    {
        $this->href = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function setKey(string $value)
    {
        $this->key = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): string
    {
        return rex_path::addon(self::ADDON, $this->path);
    }

    /**
     * {@inheritdoc}
     */
    public function setPath(string $value)
    {
        $this->path = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle(string $value)
    {
        $this->title = $value;
    }
}
