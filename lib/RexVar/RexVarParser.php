<?php

/**
 * This file is part of the Cheatsheet package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cheatsheet\RexVar;

use Cheatsheet\ParserAbstract;
use Cheatsheet\Str;

class RexVarParser extends ParserAbstract
{
    const PATTERN = '
        @
        (?<complete>
            \/\*
            .*?
            \*\/
            \s*
            class\s+rex_var_
            (?<name>[a-z_]+)
            \s+
            extends\s+rex_var
        )
        @isx';


    public function parse()
    {
        $results = [];

        /** @var \SplFileInfo $file */
        foreach ($this->iterator as $file) {
            $filepath = $file->getPathname();
            $content = \rex_file::get($filepath);

            $path = explode(DIRECTORY_SEPARATOR, str_replace(\rex_path::src(), '', $filepath));
            $cacheFilename = 'core.cache';
            if(isset($path[0]) && 'addons' === $path[0] && isset($path[2]) && 'plugins' === $path[2]) {
                $cacheFilename = $path[1] . '.' . $path[3] . '.cache';
            } elseif(isset($path[0]) && 'addons' === $path[0] && isset($path[1]) && '' !== $path[1]) {
                $cacheFilename = $path[1] . '.cache';
            }

            preg_match_all(self::PATTERN, $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
            if (count($matches) > 0) {
                foreach ($matches as $match) {

                    list($before) = str_split($content, $match['complete'][1]);
                    $lineNumber = strlen($before) - strlen(str_replace("\n", '', $before)) + 1;

                    $complete = trim($match['complete'][0], "\t\n\r\0\x0B");

                    $results[] = [
                        'internal::cacheFilename' => $cacheFilename,
                        'complete' => $complete,
                        'name' => 'REX_' . strtoupper(trim($match['name'][0])),
                        'filepath' => $filepath,
                        'filename' => $file->getFilename(),
                        'ln' => $lineNumber,
                    ];
                }
            }
        }

        if (count($results) > 0) {
            usort($results, '\Cheatsheet\Arr::sortByName');
        }

        $this->generateCache('rex_vars', $results);

        return $results;
    }
}
