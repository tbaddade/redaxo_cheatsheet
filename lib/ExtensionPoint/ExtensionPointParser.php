<?php

/**
 * This file is part of the Cheatsheet package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cheatsheet\ExtensionPoint;

use Cheatsheet\ParserAbstract;
use Cheatsheet\Str;
use SplFileInfo;

class ExtensionPointParser extends ParserAbstract
{
    const PATTERN = '
        @
        (?<complete>
            \s*
            rex_extension::registerPoint\(
                \s*
                new\s*\\\?rex_extension_point\(
                    (?<params>.*?)
                \)
                \s*
            \)                                                # Extension Point schließende Klammer
            \s*
            (?(?!\)\s*{)(;|,))                                # Ende des Extension Points { oder ; oder ,
        )
        @isx';


    public function parse()
    {
        $results = [];

        /** @var SplFileInfo $file */
        foreach ($this->iterator as $file) {
            $filepath = $file->getPathname();
            $content = \rex_file::get($filepath);

            $path = explode(DIRECTORY_SEPARATOR, str_replace(\rex_path::src(), '', $filepath));
            $cacheFilename = 'core.cache';
            if(isset($path[0]) && $path[0] === 'addons' && isset($path[2]) && $path[2] === 'plugins') {
                $cacheFilename = $path[1] . '.' . $path[3] . '.cache';
            } elseif(isset($path[0]) && $path[0] === 'addons' && isset($path[1]) && $path[1] !== '') {
                $cacheFilename = $path[1] . '.cache';
            }

            preg_match_all(self::PATTERN, $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
            foreach ($matches as $match) {
                $paramsAsString = $match['params'][0];
                $paramsAsArray = Str::parseAsArray($paramsAsString);

                list($before) = str_split($content, $match['complete'][1]);
                $lineNumber = strlen($before) - strlen(str_replace("\n", '', $before)) + 1;

                $complete = trim($match['complete'][0], "\t\n\r\0\x0B");
                $spaces = strspn($complete, " ");
                if ($spaces <= 3) {
                    $complete = trim($complete);
                } else {
                    $complete = implode("\n", array_map(function($line) use ($spaces) {
                        return substr($line, $spaces);
                    }, explode("\n", $complete)));
                }

                $results[] = [
                    'internal::cacheFilename' => $cacheFilename,
                    'point' => $complete,
                    'name' => trim($paramsAsArray[0], "'"),
                    'subject' => ($paramsAsArray[1] ?? ''),
                    'params' => ($paramsAsArray[2] ?? ''),
                    'readonly' => (!isset($paramsAsArray[3]) || (($paramsAsArray[3] === 'false' || $paramsAsArray[3] === '0')) ? 'false' : $paramsAsArray[3]),
                    'filepath' => $filepath,
                    'filename' => $file->getFilename(),
                    'ln' => $lineNumber,
                ];
            }
        }

        if (count($results) > 0) {
            usort($results, '\Cheatsheet\Arr::sortByName');
        }

        $this->generateCache('extension_points', $results);

        return $results;
    }
}
