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

use Cheatsheet\Page;
use Cheatsheet\Support\ServiceProvider;
use rex_be_page;

class ExtensionPointServiceProvider extends ServiceProvider
{

    /**
     * {@inheritdoc}
     */
    public function i18n(): string
    {
        return __DIR__ . '/lang';
    }

    /**
     * {@inheritdoc}
     */
    public function page(): rex_be_page
    {
        $page = new rex_be_page('extension-points', \rex_i18n::msg('cheatsheet_extension_point_title'));
        $page->setHref(['page' => 'cheatsheet/extension-points']);

        $subpage = new rex_be_page('docs', \rex_i18n::msg('cheatsheet_extension_point_docs_title'));
        $subpage->setHref(['page' => 'cheatsheet/extension-points/docs']);
        $subpage->setSubPath(\rex_path::addon('cheatsheet', 'lib/ExtensionPoint/pages/docs.php'));
        $page->addSubpage($subpage);

        $subpage = new rex_be_page('parse', \rex_i18n::msg('cheatsheet_extension_point_parse_title'));
        $subpage->setHref(['page' => 'cheatsheet/extension-points/parse']);
        $subpage->setSubPath(\rex_path::addon('cheatsheet', 'lib/ExtensionPoint/pages/parse.php'));
        $page->addSubpage($subpage);

        return $page;
    }
}
