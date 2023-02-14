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

use Cheatsheet\Support\ServiceProvider;
use rex_be_page;

class RexVarServiceProvider extends ServiceProvider
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
        $page = new rex_be_page('rex-var', \rex_i18n::msg('cheatsheet_rex_var_title'));
        $page->setHref(['page' => 'cheatsheet/rex-var']);

        $subpage = new rex_be_page('docs', \rex_i18n::msg('cheatsheet_rex_var_docs_title'));
        $subpage->setHref(['page' => 'cheatsheet/rex-var/docs']);
        $subpage->setSubPath(\rex_path::addon('cheatsheet', 'lib/RexVar/pages/docs.php'));
        $page->addSubpage($subpage);

        return $page;

    }
}
