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

abstract class ParserAbstract implements ParserInterface
{
    use \rex_factory_trait;

    protected $dir;
    protected $iterator;
    protected $count = 0;

    /**
     * Contructor.
     *
     * @param string $dir
     */
    private function __construct($dir)
    {
        $this->dir = $dir;
        $this->iterator = \rex_finder::factory($this->dir)->recursive()->filesOnly()->ignoreFiles(['.*', 'extension_test.*']);
    }

    /**
     * Returns a new Parser object.
     *
     * @param string $dir Path to a directory
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public static function factory($dir)
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException('Folder "' . $dir . '" not found!');
        }

        $class = static::getFactoryClass();
        return new $class($dir);
    }


    protected function generateCache($cacheDir, array $results)
    {
        $cacheDir = \rex_path::addonCache('cheatsheet', $cacheDir . '/');

        $cacheFiles = [];
        foreach ($results as $index => $result) {
            $filename = $result['internal::cacheFilename'];
            unset($result['internal::cacheFilename']);
            $cacheFiles[$filename][] = $result;
        }

        foreach ($cacheFiles as $filename => $data) {
            if (\rex_file::putCache($cacheDir . $filename, $data) === false) {
                throw new \rex_exception('Cheatsheet cache file could not be generated');
            }
        }
    }
}
