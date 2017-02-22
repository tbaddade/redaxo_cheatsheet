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

class Parser
{
    use \rex_factory_trait;

    private $dir;
    private $iterator;

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

    /**
     * Creates a Parser instance, according to the provided kind.
     *
     * @param string $kind callable parseer class
     *
     * @return ParserInterface The parser instance
     */
    public function create($kind) {
        $class = $kind . 'Parser';
        return new $class($this->iterator);
    }
}
