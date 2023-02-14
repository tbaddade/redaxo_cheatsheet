<?php

use Cheatsheet\ExtensionPoint\ExtensionPoint;
use Cheatsheet\Package;

$all = ExtensionPoint::getAll();
$requestIndex = rex_request('index', 'string', '');

if (count($all) > 1) {
    $indexList = [];
    $nav = [];
    /** @var list<ExtensionPoint> $extensionPoints */
    foreach ($all as $index => $extensionPoints) {
        $navAttributes = [
            'href' => rex_url::currentBackendPage(['index' => $index]),
        ];
        if ($index === $requestIndex) {
            $navAttributes['class'][] = 'active';
        }
        if (strpos($index, '/') !== false) {
            $navAttributes['class'][] = 'is-plugin';
        }
        $nav[] = '<a' . rex_string::buildAttributes($navAttributes) . '>' . $index . '</a>';

        /* @var ExtensionPoint $extensionPoint */
        foreach ($extensionPoints as $extensionPoint) {
            $indexList[$extensionPoint->getName() . $extensionPoint->getLn()] = '<a href="' . rex_url::currentBackendPage(['index' => $index]) . '#' . $extensionPoint->getName() . '">' . $extensionPoint->getName() . '</a>';
        }
    }


    $content = '';
    if ('' === $requestIndex) {
        ksort($indexList);
        $chunkIndex = [];
        foreach ($indexList as $index => $indexItem) {
            $letter = substr($index, 0, 1);
            $chunkIndex[$letter][] = $indexItem;
        }
        $content .= '<nav class="cheatsheet-docs-toc cheatsheet-docs-toc-columns">';
        foreach ($chunkIndex as $letter => $indexItems) {
            $content .= '<h3 class="cheatsheet-docs-toc-heading">' . $letter . '</h3>';
            $content .= '<ul class="cheatsheet-docs-toc-list"><li>' . implode('</li><li>', $indexItems) . '</li></ul>';
        }
        $content .= '</nav>';
    } else {
        $editor = rex_editor::factory();
        $toc = [];
        $extensionPoints = ExtensionPoint::getByPackage($requestIndex);
        foreach ($extensionPoints as $extensionPoint) {
            $toc[] = '<a href="#' . $extensionPoint->getName() . '">' . $extensionPoint->getName() . '</a>';
            $button = '';
            $url = $editor->getUrl($extensionPoint->getFilepath(),$extensionPoint->getLn());
            if (is_string($url)) {
                $supportedEditors = $editor->getSupportedEditors();
                $button = ' <a href="'. $url .'" class="btn btn-default btn-xs"><i class="rex-icon rex-icon-view"></i> '.(isset($supportedEditors[$editor->getName()]) ? $supportedEditors[$editor->getName()] : $editor->getName()).'</a>';
            }
            $docs = '';
            $docs .= '<div class="cheatsheet-docs-block">';
            $docs .= '<a name="' . $extensionPoint->getName() . '"></a>';
            $docs .= '<h3 class="cheatsheet-docs-code-heading">' . $extensionPoint->getName() . '</h3>';

            $docs .= '<table class="cheatsheet-docs-table"><colgroup><col width="180px" /><col width="*" /></colgroup>';
            $docs .= '<tfoot>';
            $docs .= '<tr><th>' . rex_i18n::msg('cheatsheet_extension_point_register') . '</th><td><p><span class="text-muted">#' . str_replace('~', '&nbsp;', str_pad($extensionPoint->getLn(), 6, '~')) . '</span> ' . str_replace(\rex_path::src(), '', $extensionPoint->getFilepath()) . $button . '</p><pre>' . $extensionPoint->getRegisteredPoint() . '</pre></td></tr>';
            $docs .= '</tfoot><tbody>';
            $docs .= '<tr><th>' . rex_i18n::msg('cheatsheet_extension_point_subject') . '</th><td>' . ('' !== $extensionPoint->getSubject() ? '<pre>' . $extensionPoint->getSubject() . '</pre>' : '') . '</td></tr>';
            $docs .= '<tr><th>' . rex_i18n::msg('cheatsheet_extension_point_parameter') . '</th><td>' . ('' !== $extensionPoint->getParams() ? '<pre>' . $extensionPoint->getParams() . '</pre>' : '') . '</td></tr>';
            $docs .= '<tr><th>' . rex_i18n::msg('cheatsheet_extension_point_readonly') . '</th><td>' . ($extensionPoint->isReadonly() ? rex_i18n::msg('cheatsheet_extension_point_yes') : rex_i18n::msg('cheatsheet_extension_point_no')) . '</td></tr>';
            $docs .= '</tbody></table>';
            $docs .= '</div>';

            $content .= $docs;
        }

        $content = '<nav class="cheatsheet-docs-toc"><ul class="cheatsheet-docs-toc-list"><li>' . implode('</li><li>', $toc) . '</li></ul></nav>' . $content;
    }


    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('cheatsheet_extension_point_package_heading'));
    $fragment->setVar('body', '<nav class="cheatsheet-docs-navigation"><ul><li>' . implode('</li><li>', $nav) . '</li></ul></nav>', false);
    $fragment->setVar('options', '<a class="btn btn-xs btn-default" href="' .  rex_url::currentBackendPage(['index' => '']). '">' . \rex_i18n::msg('cheatsheet_extension_point_index') . '</a>', false);
    $sidebar = $fragment->parse('core/page/section.php');


    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('cheatsheet_extension_point_title') . ' : ' . ('' === $requestIndex ? rex_i18n::msg('cheatsheet_extension_point_index') : '<code>' . $requestIndex . '</code>' ), false);
    $fragment->setVar('body', $content, false);
    $content = $fragment->parse('core/page/section.php');


    echo '
    <section class="cheatsheet-docs">
        <div class="cheatsheet-docs-sidebar">' . $sidebar . '</div>
        <div class="cheatsheet-docs-content">' . $content . '</div>
    </section>';
} else {
    echo rex_view::warning(rex_i18n::msg('cheatsheet_extension_point_no_docs'));
}
