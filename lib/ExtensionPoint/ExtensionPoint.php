<?php

/**
 * This file is part of the Cheatsheet package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cheatsheet\ExtensionPoint;

class ExtensionPoint
{
    private static $cacheLoaded = false;
    private static $extensionPoints = [];

    private $ln;
    private $name;
    private $filename;
    private $filepath;
    private $params;
    private $point;
    private $readonly;
    private $subject;

    private function __construct()
    {
    }

    /**
     * Checks if the given extension point exists.
     *
     * @param string $package Package name
     *
     * @return bool
     */
    public static function exists($package)
    {
        self::checkCache();
        return isset(self::$extensionPoints[$package]);
    }

    /**
     * Returns the extension points for the given package.
     *
     * @param string $package Package name
     *
     * @return self[]
     */
    public static function getByPackage($package)
    {
        if (self::exists($package)) {
            return self::$extensionPoints[$package];
        }
        return null;
    }

    /**
     * Returns the core extension points.
     *
     * @return array
     */
    public static function getFromCore()
    {
        return self::getByPackage('core');
    }

    /**
     * Returns the addon extension points.
     *
     * @param string $addon Addon name
     *
     * @return array
     */
    public static function getFromAddon($addon)
    {
        return self::getByPackage($addon);
    }

    /**
     * Returns the addon extension points.
     *
     * @param string $addon Addon name
     * @param string $plugin Plugin name
     *
     * @return array
     */
    public static function getFromPlugin($addon, $plugin)
    {
        return self::getByPackage($addon . '/' . $plugin);
    }

    /**
     * Returns the filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Returns the filename.
     *
     * @return string
     */
    public function getFilepath()
    {
        return $this->filepath;
    }

    /**
     * Returns the line number.
     *
     * @return string
     */
    public function getLn()
    {
        return $this->ln;
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the parameters.
     *
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Returns the parameters.
     *
     * @return string
     */
    public function getRegisteredPoint()
    {
        return $this->point;
    }

    /**
     * Returns the subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Returns the readonly.
     *
     * @return bool
     */
    public function isReadonly()
    {
        return ($this->readonly == 'false' ? false : true);
    }

    /**
     * Counts the extension points.
     *
     * @return int
     */
    public static function count()
    {
        self::checkCache();
        return count(self::$extensionPoints);
    }

    /**
     * Returns an array of all Extension Points.
     *
     * @return self[]
     */
    public static function getAll()
    {
        self::checkCache();
        return self::$extensionPoints;
    }

    /**
     * Loads the cache if not already loaded.
     */
    private static function checkCache()
    {
        if (self::$cacheLoaded) {
            return;
        }

        $cacheDir = \rex_path::addonCache('cheatsheet/extension_points/');
        if (!file_exists($cacheDir)) {
            return;
        }


        $iterator = \rex_finder::factory($cacheDir)->filesOnly();

        /* @var $file \SplFileInfo */
        foreach ($iterator as $file) {
            $cacheKey = str_replace(['.' . $file->getExtension(), '.'], ['', '/'], $file->getFilename());
            $cacheExtensionPoints = \rex_file::getCache($file->getPathname());
            if ($cacheExtensionPoints != '') {
                foreach ($cacheExtensionPoints as $cacheExtensionPoint) {
                    $extensionPoint = new self();
                    foreach ($cacheExtensionPoint as $key => $value) {
                        $extensionPoint->$key = $value;
                    }
                    self::$extensionPoints[$cacheKey][] = $extensionPoint;
                }
            }
        }

        self::$cacheLoaded = true;
    }

    /**
     * Resets the intern cache of this class.
     */
    public static function reset()
    {
        self::$cacheLoaded = false;
        self::$extensionPoints = [];
    }
}
