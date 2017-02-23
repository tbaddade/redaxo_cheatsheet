<?php

use Cheatsheet\RexVar\RexVar;
use Cheatsheet\RexVar\RexVarParser;
use Cheatsheet\Package;

$all = RexVar::getAll();
$requestIndex = rex_request('index', 'string', 'core');
$function = rex_request('func', 'string', '');

if ($function == 'parse') {
    RexVar::deleteCache();
    $rexVars = RexVarParser::factory(rex_path::src())->parse();
    if (count($rexVars)) {
        echo rex_view::success(rex_i18n::msg('cheatsheet_rex_var_parse_success_all', count($rexVars)));
    }
}


if (count($all)) {
    $nav = [];
    $toc = [];
    $content = [];
    foreach ($all as $index => $rexVars) {
        $navAttributes = [
            'href' => rex_url::currentBackendPage(['index' => $index]),
        ];
        if ($index == $requestIndex) {
            $navAttributes['class'][] = 'active';
        }
        if (strpos($index, '/') !== false) {
            $navAttributes['class'][] = 'is-plugin';
        }
        $nav[] = '<a' . rex_string::buildAttributes($navAttributes) . '>' . $index . '</a>';
    }


    $rexVars = RexVar::getByPackage($requestIndex);
    foreach ($rexVars as $rexVar) {
        $toc[] = '<a href="#' . $rexVar->getName() . '">' . $rexVar->getName() . '</a>';

        $docs = '';
        $docs .= '<div class="cheatsheet-docs-block">';
        $docs .= '<a name="' . $rexVar->getName() . '"></a>';
        $docs .= '<h3 class="cheatsheet-docs-code-heading">' . $rexVar->getName() . '</h3>';

        $docs .= '<table class="cheatsheet-docs-table"><colgroup><col width="180px" /><col width="*" /></colgroup>';
        $docs .= '<tbody>';
        $docs .= '<tr><th>' . rex_i18n::msg('cheatsheet_rex_var_register') . '</th><td><p><span class="text-muted">#' . str_replace('~', '&nbsp;', str_pad($rexVar->getLn(), 6, '~')) . '</span> ' . str_replace(\rex_path::src(), '', $rexVar->getFilepath()) . '</p><pre>' . $rexVar->getRegistered() . '</pre></td></tr>';
        $docs .= '</tbody></table>';
        $docs .= '</div>';

        $content[] = $docs;
    }

    $package = Package::get($requestIndex);

    $fragment = new rex_fragment();
    $fragment->setVar('title', $package->getName());
    $fragment->setVar('body', '<nav class="cheatsheet-docs-toc"><ul><li>' . implode('</li><li>', $toc) . '</li></ul></nav>' . implode('', $content), false);
    $content = $fragment->parse('core/page/section.php');

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('cheatsheet_rex_var_navigation_heading'));
    $fragment->setVar('body', '<nav class="cheatsheet-docs-navigation"><ul><li>' . implode('</li><li>', $nav) . '</li></ul></nav>', false);
    $fragment->setVar('options', '<a class="btn btn-xs btn-default" href="' .  rex_url::currentBackendPage(['func' => 'parse']). '">' . \rex_i18n::msg('cheatsheet_rex_var_parse_all') . '</a>', false);
    $sidebar = $fragment->parse('core/page/section.php');


    echo '
    <section class="cheatsheet-docs">
        <div class="cheatsheet-docs-sidebar">' . $sidebar . '</div>
        <div class="cheatsheet-docs-content">' . $content . '</div>
    </section>';
} else {
    echo rex_view::warning(rex_i18n::msg('cheatsheet_rex_var_no_docs'));
}
