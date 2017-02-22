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

class Arr
{
    public static function sortByName($a, $b) {
        return strcmp($a['name'], $b['name']);
    }
}
