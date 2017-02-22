<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cheatsheet\ExtensionPoint;
use Cheatsheet\Parser;
use Cheatsheet\Str;


function getTable($extensionPoints) {
    $rows = '';
    $docs = '';
    /** @var ExtensionPoint $extensionPoint **/
    foreach ($extensionPoints as $extensionPoint) {
        $rows .= '
            <tr>
                <td><code>' . $extensionPoint->getName() . '</code></td>
                <td>' . ($extensionPoint->getSubject() != '' ? '<small><code>' . $extensionPoint->getSubject() . '</code></small>' : '') . '</td>
                <td>' . ($extensionPoint->getParams() != '' ? '<small><code>' . $extensionPoint->getParams() . '</code></small>' : '') . '</td>
                <td>' . ($extensionPoint->isReadonly() ? '<small><code>true</code></small>' : '') . '</td>
                <td><small class="text-muted">#' . str_replace('~', '&nbsp;', str_pad($extensionPoint->getLn(), 6, '~')) . '</small> <small>' . str_replace(\rex_path::src(), '', $extensionPoint->getFilepath()) . '</small></td>
            </tr>';

$etc = <<<'EOT'
rex_extension::register('CLANG_DELETED', function (rex_extension_point $ep) {
    $del = rex_sql::factory();
    $del->setQuery('delete from ' . rex::getTablePrefix() . 'article where clang_id=?', [$ep->getParam('clang')->getId()]);
});
EOT;

        $docs .= '### ' . $extensionPoint->getName() . "\n\n";
/*
        $docs .= '**Registrierung im Code**' . "\n";
        $docs .= '<span class="text-muted">#' . str_replace('~', '&nbsp;', str_pad($extensionPoint->getLn(), 6, '~')) . '</span> ' . str_replace(\rex_path::src(), '', $extensionPoint->getFilepath()) . '' . "\n";
        $docs .= '```' . "\n";
        $docs .= $extensionPoint->getRegisteredPoint() . "\n";
        $docs .= '```' . "\n";
*/
        $docs .= '<table style="font-size: 85%;"><colgroup><col width="140px" /><col width="*" /></colgroup><tbody>';
        $docs .= '<tr><th style="font-size: inherit;">Registrierung</th><td><span class="text-muted">#' . str_replace('~', '&nbsp;', str_pad($extensionPoint->getLn(), 6, '~')) . '</span> ' . str_replace(\rex_path::src(), '', $extensionPoint->getFilepath()) . '<br /><pre>' . $extensionPoint->getRegisteredPoint() . '</pre></td></tr>';
        $docs .= '<tr><th style="font-size: inherit;">Daten</th><td>' . ($extensionPoint->getSubject() != '' ? '<pre>' . $extensionPoint->getSubject() . '</pre>' : '') . '</td></tr>';
        $docs .= '<tr><th style="font-size: inherit;">Parameter</th><td>' . ($extensionPoint->getParams() != '' ? '<code>' . $extensionPoint->getParams() . '</code>' : '') . '</td></tr>';
        $docs .= '<tr><th style="font-size: inherit;">Schreibgeschützt</th><td>' . ($extensionPoint->isReadonly() ? '<code>true</code>' : 'false') . '</td></tr>';

        $docs .= '<tr><th style="font-size: inherit;">Beispiel</th><td><pre>' . $etc . '</pre></td></tr>';
        $docs .= '</tbody></table>' . "\n\n";
        $docs .= '  ' . "\n\n";
    }
    $table = '
        <table class="table">
            <colgroup>
                <col width="15%" />
                <col width="20%" />
                <col width="30%" />
                <col width="5%" />
                <col width="*" />
            </colgroup>
            <thead>
                <tr>
                    <th>Extension Point</th>
                    <th>Subject</th>
                    <th>Params</th>
                    <th>Readonly</th>
                    <th>Pfad</th>
                </tr>
            </thead>
            <tbody>
                ' . $rows . '
            </tbody>
        </table>';

    $docs = '<div class="panel-body"><div class="rex-docs" style="font-family: "Fira Code", -apple-system;">' . rex_markdown::factory()->parse($docs) . '</div></div>';

    return $table . $docs;
}


$packageRequest = trim(rex_request('package', 'string', ''), '/ ');
$function = rex_request('function', 'string', 'view');

$packages = rex_package::getRegisteredPackages();
$package = isset($packages[$packageRequest]) ? $packages[$packageRequest] : 'core';
$configPackage = [
    'key' => 'core',
    'name' => 'core',
    'path' => rex_path::core(),
    'title' => 'Core'
];
if ($package instanceof rex_addon || $package instanceof rex_plugin) {
    $key = ($package instanceof rex_addon) ? $package->getName() : $package->getAddon()->getName() . '/' . $package->getName();
    $page = $package->getProperty('page');
    $title = isset($page['title']) ? \rex_i18n::translate($page['title']) : $package->getName();
    $configPackage = [
        'key' => $key,
        'name' => $package->getName(),
        'path' => $package->getPath(),
        'title' => $title,
    ];
}

//var_dump($configPackage);

if ($function == 'parse') {
    $path = $configPackage['path'];
    $parser = Parser::factory($path)->create('ExtensionPoint');
    $extensionPoints = $parser->parse();
    if (count($extensionPoints)) {
        echo rex_view::success('Extension Points für <b>' . $configPackage['title'] . '</b> gefunden');
    } else {
        echo rex_view::warning('Keine Extension Points für <b>' . $configPackage['title'] . '</b> gefunden');
    }
}

$navi = [];
$navi['Core'][] = '
    <li>
        <a href="' . rex_url::currentBackendPage(['package' => 'core', 'function' => 'view']) . '">anzeigen</a>
        <a class="pull-right" href="' . rex_url::currentBackendPage(['package' => 'core', 'function' => 'parse']) . '"><small>parsen</small></a>
    </li>';

$addons = rex_addon::getAvailableAddons();
$foundAddonItems = [];
$notFoundAddonItems = [];
foreach ($addons as $addon) {
    $extensionPoints = ExtensionPoint::getFromAddon($addon->getName());
    if (count($extensionPoints)) {
        $pluginList = '';
        if ($addon->getAvailablePlugins()) {
            $pluginItems = [];
            foreach ($addon->getAvailablePlugins() as $plugin) {
                $extensionPoints = ExtensionPoint::getFromPlugin($addon->getName(), $plugin->getName());
                if (count($extensionPoints)) {
                    $pluginItems[] = '<li><a href="' . rex_url::currentBackendPage(['package' => $addon->getName() . '/' . $plugin->getName(), 'function' => 'view']) . '">' . Str::getPackageTitle([$addon->getName(), $plugin->getName()]) . '</a></li>';
                }
            }
            $pluginList = (count($pluginItems)) ? '<ul>' . implode('', $pluginItems) . '</ul>' : '';
        }

        $foundAddonItems[] = '<li><a href="' . rex_url::currentBackendPage(['package' => $addon->getName(), 'function' => 'view']) . '">' . Str::getPackageTitle([$addon->getName()]) . '</a> <a class="pull-right" href="' . rex_url::currentBackendPage(['package' => $addon->getName(), 'function' => 'parse']) . '"><small>erneut parsen</small></a>' . $pluginList . '</li>';
    } else {
        $notFoundAddonItems[] = '<li>' . Str::getPackageTitle([$addon->getName()]) . ' <a class="pull-right" href="' . rex_url::currentBackendPage(['package' => $addon->getName(), 'function' => 'parse']) . '"><small>parsen</small></a></li>';
    }
}
$navi['AddOns'] = $foundAddonItems;
$navi['AddOns ohne Extension Points'] = $notFoundAddonItems;

$left = '';
foreach ($navi as $title => $level) {
    $left .= '<li>' . $title . '<ul>' . implode('', $level) . '</ul></li>';
}
$fragment = new rex_fragment();
$fragment->setVar('title', 'Extension Points');
$fragment->setVar('body', '<nav class="cheatsheet-docs-navigation"><ul>' . $left . '</ul></nav>', false);
$sidebar = $fragment->parse('core/page/section.php');


$content = '';
$extensionPoints = ExtensionPoint::get($configPackage['key']);
if ($extensionPoints) {
    $table = getTable($extensionPoints);
    $fragment = new rex_fragment();
    $fragment->setVar('title', $configPackage['title']);
    $fragment->setVar('content', $table, false);
    $content = $fragment->parse('core/page/section.php');
}


echo '
<section class="cheatsheet-docs">
    <div class="row">
        <div class="col-md-3 cheatsheet-docs-sidebar">' . $sidebar . '</div>
        <div class="col-md-9 cheatsheet-docs-content">' . $content . '</div>
    </div>
</section>
';
