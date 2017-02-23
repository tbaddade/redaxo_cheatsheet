<?php

use Cheatsheet\ExtensionPoint\ExtensionPoint;
use Cheatsheet\Package;

$all = ExtensionPoint::getAll();
$requestIndex = rex_request('index', 'string', 'core');

if (count($all)) {
    $nav = [];
    $toc = [];
    $content = [];
    foreach ($all as $index => $extensionPoints) {
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


    $extensionPoints = ExtensionPoint::getByPackage($requestIndex);
    foreach ($extensionPoints as $extensionPoint) {
        $toc[] = '<a href="#' . $extensionPoint->getName() . '">' . $extensionPoint->getName() . '</a>';

        $docs = '';
        $docs .= '<div class="cheatsheet-docs-block">';
        $docs .= '<a name="' . $extensionPoint->getName() . '"></a>';
        $docs .= '<h3 class="cheatsheet-docs-code-heading">' . $extensionPoint->getName() . '</h3>';

        $docs .= '<table class="cheatsheet-docs-table"><colgroup><col width="180px" /><col width="*" /></colgroup>';
        $docs .= '<tfoot>';
        $docs .= '<tr><th>' . rex_i18n::msg('cheatsheet_extension_point_register') . '</th><td><p><span class="text-muted">#' . str_replace('~', '&nbsp;', str_pad($extensionPoint->getLn(), 6, '~')) . '</span> ' . str_replace(\rex_path::src(), '', $extensionPoint->getFilepath()) . '</p><pre>' . $extensionPoint->getRegisteredPoint() . '</pre></td></tr>';
        $docs .= '</tfoot><tbody>';
        $docs .= '<tr><th>' . rex_i18n::msg('cheatsheet_extension_point_subject') . '</th><td>' . ($extensionPoint->getSubject() != '' ? '<pre>' . $extensionPoint->getSubject() . '</pre>' : '') . '</td></tr>';
        $docs .= '<tr><th>' . rex_i18n::msg('cheatsheet_extension_point_parameter') . '</th><td>' . ($extensionPoint->getParams() != '' ? '<pre>' . $extensionPoint->getParams() . '</pre>' : '') . '</td></tr>';
        $docs .= '<tr><th>' . rex_i18n::msg('cheatsheet_extension_point_readonly') . '</th><td>' . ($extensionPoint->isReadonly() ? rex_i18n::msg('cheatsheet_extension_point_yes') : rex_i18n::msg('cheatsheet_extension_point_no')) . '</td></tr>';
        $docs .= '</tbody></table>';
        $docs .= '</div>';

        $content[] = $docs; //\rex_markdown::factory()->parse($docs);
    }

    $package = Package::get($requestIndex);

    $fragment = new rex_fragment();
    $fragment->setVar('title', $package->getName());
    $fragment->setVar('body', '<nav class="cheatsheet-docs-toc"><ul><li>' . implode('</li><li>', $toc) . '</li></ul></nav>' . implode('', $content), false);
    $content = $fragment->parse('core/page/section.php');

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('cheatsheet_extension_point_navigation_heading'));
    $fragment->setVar('body', '<nav class="cheatsheet-docs-navigation"><ul><li>' . implode('</li><li>', $nav) . '</li></ul></nav>', false);
    $sidebar = $fragment->parse('core/page/section.php');


    echo '
    <section class="cheatsheet-docs">
        <div class="cheatsheet-docs-sidebar">' . $sidebar . '</div>
        <div class="cheatsheet-docs-content">' . $content . '</div>
    </section>';
} else {
    echo rex_view::warning(rex_i18n::msg('cheatsheet_extension_point_no_docs'));
}
