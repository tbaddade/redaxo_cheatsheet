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
        }

        return $package;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($value)
    {
        $this->name = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getPackageId()
    {
        return $this->packageId;
    }

    /**
     * {@inheritdoc}
     */
    public function setPackageId($value)
    {
        $this->packageId = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($value)
    {
        $this->path = $value;
    }

}
