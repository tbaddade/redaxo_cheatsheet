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
    protected $iterator;
    protected $count = 0;


    public function __construct(\rex_finder $iterator)
    {
        $this->iterator = $iterator;
    }

    protected function generateCache($cacheDir, array $results)
    {
        $cacheDir = \rex_path::addonCache('cheatsheet', $cacheDir . '/');

        $cacheFiles = [];
        foreach ($results as $result) {
            $filename = $result['internal::cacheFilename'];
            unset($result['internal::cacheFilename']);
            $cacheFiles[$filename][] = $result;
        }

        foreach ($cacheFiles as $filename => $data) {
            if (\rex_file::putCache($cacheDir . $filename, $data) === false) {
                throw new \rex_exception('Clang cache file could not be generated');
            }
        }
    }

    public function parse()
    {

    }
}
