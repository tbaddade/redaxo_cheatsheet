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

use Cheatsheet\Page;
use Cheatsheet\Support\ServiceProvider;

class RexVarServiceProvider extends ServiceProvider
{

    /**
     * {@inheritdoc}
     */
    public function i18n()
    {
        return __DIR__ . '/lang';
    }

    /**
     * {@inheritdoc}
     */
    public function page()
    {
        $page = new Page();
        $page->setKey('rex-var');
        $page->setPath('lib/RexVar/pages/rex-var.php');
        $page->setTitle(\rex_i18n::msg('cheatsheet_rex_var_title'));

        $subpage = new Page();
        $subpage->setKey('docs');
        $subpage->setPath('lib/RexVar/pages/docs.php');
        $subpage->setTitle(\rex_i18n::msg('cheatsheet_rex_var_docs_title'));
        $page->addSubpage($subpage->get());

        //$subpage = new Page();
        //$subpage->setKey('parse');
        //$subpage->setPath('lib/ExtensionPoint/pages/parse.php');
        //$subpage->setTitle(\rex_i18n::msg('cheatsheet_extension_point_parse_title'));
        //$page->addSubpage($subpage->get());

        return $page->get();
    }
}