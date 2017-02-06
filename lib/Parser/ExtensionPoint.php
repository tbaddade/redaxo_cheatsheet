<?php

/**
 * This file is part of the Cheatsheet package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cheatsheet\Parser;

use Cheatsheet\ParserAbstract;
use Cheatsheet\Str;

class ExtensionPoint extends ParserAbstract
{
    const PATTERN = '
        @
        rex_extension::registerPoint\(
        \s*
        new\s*\\\?rex_extension_point\(
        (.*?)
        \)
        \s*
        \)                                                # Extension Point schließende Klammer
        \s*
        (?(?!\)\s*{)(;|,))                                # Ende des Extension Points { oder ; oder ,
        @isx';


    public function parse()
    {
        $results = [];
        foreach ($this->iterator as $file) {
            $filepath = $file->getPathname();
            $content = \rex_file::get($filepath);
            preg_match_all(self::PATTERN, $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
            if (count($matches)) {
                foreach ($matches as $match) {
                    $paramsAsString = $match[1][0];
                    $paramsAsArray = Str::parseAsArray($paramsAsString);

                    list($before) = str_split($content, $match[0][1]);
                    $lineNumber = strlen($before) - strlen(str_replace("\n", '', $before)) + 1;

                    $path = explode(DIRECTORY_SEPARATOR, str_replace(\rex_path::src(), '', $filepath));
                    $cacheFilename = 'core.cache';
                    if(isset($path[0]) && $path[0] == 'addons' && isset($path[2]) && $path[2] == 'plugins') {
                        $cacheFilename = $path[1] . '.' . $path[3] . '.cache';
                    } elseif(isset($path[0]) && $path[0] == 'addons' && isset($path[1]) && $path[1] != '') {
                        $cacheFilename = $path[1] . '.cache';
                    }

                    $results[] = [
                        'internal::cacheFilename' => $cacheFilename,
                        'name' => trim($paramsAsArray[0], "'"),
                        'subject' => (isset($paramsAsArray[1]) ? $paramsAsArray[1] : ''),
                        'params' => (isset($paramsAsArray[2]) ? $paramsAsArray[2] : ''),
                        'readonly' => (!isset($paramsAsArray[3]) || (isset($paramsAsArray[3]) && ($paramsAsArray[3] == 'false' || $paramsAsArray[3] == '0')) ? 'false' : $paramsAsArray[3]),
                        'filepath' => $filepath,
                        'filename' => $file->getFilename(),
                        'ln' => $lineNumber,
                    ];
                }
            }
        }

        if (count($results)) {
            usort($results, '\Cheatsheet\Arr::sortByName');
        }

        $this->generateCache('extension_points', $results);

        return $results;
    }
}
