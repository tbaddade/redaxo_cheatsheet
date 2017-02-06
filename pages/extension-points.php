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

    return $table;
}


$packageRequest = trim(rex_request('package', 'string', ''), '/ ');
$packageParts = explode('/', $packageRequest);
$function = rex_request('function', 'string', 'view');

$package = 'core';
$packageTitle = ucwords($package);
if (rex_addon::exists($packageParts[0]) || (count($packageParts) == 2 && rex_plugin::exists($packageParts[0], $packageParts[1]))) {
    $package = $packageRequest;
    $packageTitle = Str::getPackageTitle($packageParts);
}

if ($function == 'parse') {
    $path = rex_path::addon($package);
    if ($package == 'core') {
        $path = rex_path::core();
    }
    $parser = Parser::factory($path)->create(Parser::EXTENSION_POINTS);
    $extensionPoints = $parser->parse();
    if (count($extensionPoints)) {
        echo rex_view::success('Extension Points für <b>' . $package . '</b> gefunden');
    } else {
        echo rex_view::warning('Keine Extension Points für <b>' . $package . '</b> gefunden');
        $package = 'core';
        $packageTitle = ucwords($package);
    }
}

$navi = [];
$navi['Core'][] = '<li><a href="' . rex_url::currentBackendPage(['package' => 'core', 'function' => 'view']) . '">anzeigen</a></li>';
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
$extensionPoints = ExtensionPoint::get($package);
if ($extensionPoints) {
    $table = getTable($extensionPoints);
    $fragment = new rex_fragment();
    $fragment->setVar('title', $packageTitle);
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
