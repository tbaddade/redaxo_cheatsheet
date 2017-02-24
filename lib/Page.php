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


class Page implements PageInterface
{
    const ADDON = 'cheatsheet';

    private $href;
    private $key;
    private $path;
    private $title;
    private $subpages = [];

    public function __construct()
    {
        $this->subpages = [];
    }


    /**
     * {@inheritdoc}
     */
    public function addSubpage(\rex_be_page $page)
    {
        $this->subpages[] = $page;
    }


    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $page = new \rex_be_page($this->getKey(), $this->getTitle());
        $page->setSubPath($this->getPath());
        if ($this->getHref()) {
            $page->setHref($this->getHref());
        } else {
            $page->setHref('index.php?page=' . self::ADDON . '/' . $this->key);
        }

        if (count($this->subpages)) {
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
    public function getHref()
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
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function setKey($value)
    {
        $this->key = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return \rex_path::addon(self::ADDON, $this->path);
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($value)
    {
        $this->path = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($value)
    {
        $this->title = $value;
    }

}
