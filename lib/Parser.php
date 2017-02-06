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

use Cheatsheet\Parser\ExtensionPoint;

class Parser
{
    use \rex_factory_trait;

    const EXTENSION_POINTS = 1;

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
     * @param int $kind One of ::EXTENSION_POINTS
     *
     * @return ParserInterface The parser instance
     */
    public function create($kind) {
        switch ($kind) {
            case self::EXTENSION_POINTS:
                return new ExtensionPoint($this->iterator);
            default:
                throw new \rex_exception(
                    'Kind must be one of ::EXTENSION_POINTS'
                );
        }
    }
}
