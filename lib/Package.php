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


class Package
{
    const CORE = 'core';

    private $name;
    private $packageId;
    private $path;
    private $plugins = [];


    public static function exists($packageId)
    {
        if (!is_string($packageId)) {
            throw new \InvalidArgumentException('Expecting $packageId to be string, but ' . gettype($packageId) . ' given!');
        }

        if ($packageId === Package::CORE) {
            return true;
        }
        if (\rex_package::exists($packageId)) {
            return true;
        }
        return false;
    }

    public static function get($packageId)
    {
        if (!is_string($packageId)) {
            throw new \InvalidArgumentException('Expecting $packageId to be string, but ' . gettype($packageId) . ' given!');
        }

        $package = new self();
        if ($packageId === Package::CORE) {
            $package->setPackageId($packageId);
            $package->setName(Package::CORE);
            $package->setPath(\rex_path::core());
        } else {
            $rexPackage = \rex_package::get($packageId);
            $package->setPackageId($packageId);
            $package->setName($rexPackage->getName());
            $package->setPath($rexPackage->getPath());

            if (strpos($packageId, '/') === false) {
                $addon = \rex_addon::get($packageId);
                $package->setPlugins($addon->getRegisteredPlugins());
            }
        }

        return $package;
    }

    /**
     * Returns the name of the package.
     *
     * @return string Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name.
     *
     * @param string $value
     */
    protected function setName($value)
    {
        $this->name = $value;
    }

    /**
     * Returns the id of the package.
     *
     * @return string PackageId
     */
    public function getPackageId()
    {
        return $this->packageId;
    }

    /**
     * Sets the packageId.
     *
     * @param string $value
     */
    protected function setPackageId($value)
    {
        $this->packageId = $value;
    }

    /**
     * Returns the path of the package.
     *
     * @return string Path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the path.
     *
     * @param string $value
     */
    protected function setPath($value)
    {
        $this->path = $value;
    }

    /**
     * Returns the plugins of the package.
     *
     * @return array Plugins
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Sets the plugins.
     *
     * @param array $value
     */
    protected function setPlugins($value)
    {
        $this->plugins = $value;
    }

}
