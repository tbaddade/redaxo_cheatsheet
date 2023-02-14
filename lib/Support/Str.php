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

use rex_addon;
use rex_i18n;
use rex_plugin;

class Str
{
    public static function getPackageTitle($packageParts, $default = '')
    {
        $package = null;
        if (2 === count($packageParts) && rex_plugin::exists($packageParts[0], $packageParts[1])) {
            $package = rex_plugin::get($packageParts[0], $packageParts[1]);
        } elseif ( rex_addon::exists($packageParts[0])) {
            $package = rex_addon::get($packageParts[0]);
        }
        if (null !== $package) {
            $page = $package->getProperty('page');
            return isset($page['title']) ? rex_i18n::translate($page['title']) : $package->getName();
        }

        return $default;
    }

    public static function parseAsArray($string)
    {
        // string bereinigen, Newlines und doppelte Leerzeichen raus
        $string = preg_replace(['/\s{2}/', '/\[\s/', '/,?\s?\]/'], ['', '[', ']'], preg_replace("/[\r\n]+/", ' ', $string));
        $values = explode(',', $string);

        $arrayOpened = false;
        $arrayKey = 0;
        foreach($values as $index => $value) {
            $value = trim($value);
            $key = $index;
            if ('[]' === substr($value, 0, 2)) {
                $values[$key] = $value;
            }
            elseif ('[' === substr($value, 0, 1)) {
                $values[$key] = $value;
                $arrayKey = $key;
                $arrayOpened = true;
                if (strpos(substr($value, 1), '[') === false && ']' === substr($value, -1)) {
                    $arrayOpened = false;
                }
            } elseif ($arrayOpened) {
                $values[$arrayKey] = $values[$arrayKey] . ', ' . $value;
                unset($values[$key]);
                if (strpos($value, '[') === false && ']' === substr($value, -1)) {
                    $arrayOpened = false;
                }
            } else {
                $values[$key] = $value;
            }
        }

        return array_values(array_diff($values, []));
    }
}
