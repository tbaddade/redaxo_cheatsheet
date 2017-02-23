<?php

/**
 * This file is part of the Cheatsheet package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cheatsheet\RexVar;

class RexVar
{
    private static $cacheLoaded = false;
    private static $rexVars = [];

    private $complete;
    private $ln;
    private $name;
    private $filename;
    private $filepath;

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
        return isset(self::$rexVars[$package]);
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
            return self::$rexVars[$package];
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
    public function getRegistered()
    {
        return $this->complete;
    }

    /**
     * Counts the rex vars.
     *
     * @return int
     */
    public static function count()
    {
        self::checkCache();
        return count(self::$rexVars);
    }

    /**
     * Returns an array of all Extension Points.
     *
     * @return self[]
     */
    public static function getAll()
    {
        self::checkCache();
        return self::$rexVars;
    }

    /**
     * Loads the cache if not already loaded.
     */
    private static function checkCache()
    {
        if (self::$cacheLoaded) {
            return;
        }

        $cacheDir = \rex_path::addonCache('cheatsheet/rex_vars/');
        if (!file_exists($cacheDir)) {
            return;
        }


        $iterator = \rex_finder::factory($cacheDir)->filesOnly();

        /* @var $file \SplFileInfo */
        foreach ($iterator as $file) {
            $cacheKey = str_replace(['.' . $file->getExtension(), '.'], ['', '/'], $file->getFilename());
            $cacheRexVars = \rex_file::getCache($file->getPathname());
            if ($cacheRexVars != '') {
                foreach ($cacheRexVars as $cacheRexVar) {
                    $rexVar = new self();
                    foreach ($cacheRexVar as $key => $value) {
                        $rexVar->$key = $value;
                    }
                    self::$rexVars[$cacheKey][] = $rexVar;
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
        self::$rexVars = [];
    }

    /**
     * Resets the intern cache of this class.
     */
    public static function deleteCache()
    {
        \rex_dir::delete(\rex_path::addonCache('cheatsheet/rex_vars/'));
        self::reset();
    }
}
