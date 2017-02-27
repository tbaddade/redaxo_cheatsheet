<?php

use Cheatsheet\ExtensionPoint\ExtensionPointParser;
use Cheatsheet\Package;


$packageRequest = trim(rex_request('package', 'string', ''), '/ ');
$function = rex_request('func', 'string', 'view');

if ($function == 'parse' && rex_request('all', 'bool', 0)) {
    $extensionPoints = ExtensionPointParser::factory(rex_path::src())->parse();
    if (count($extensionPoints)) {
        echo rex_view::success(rex_i18n::msg('cheatsheet_extension_point_parse_success_all', count($extensionPoints)));
    }
}
if ($function == 'parse' && Package::exists($packageRequest)) {
    $package = Package::get($packageRequest);

    $extensionPoints = ExtensionPointParser::factory($package->getPath())->parse();
    if (count($extensionPoints)) {
        echo rex_view::success(rex_i18n::msg('cheatsheet_extension_point_parse_success', count($extensionPoints), $package->getName()));
    } else {
        $cacheDir = \rex_path::addonCache('cheatsheet', 'extension_points/');
        $data = '';
        $filename = $package->getPackageId() . '.cache';
        if (\rex_file::putCache($cacheDir . $filename, $data) === false) {
            throw new \rex_exception('Cheatsheet cache file could not be generated');
        }
        echo rex_view::warning(rex_i18n::msg('cheatsheet_extension_point_parse_warning', $package->getName()));
    }
}



$getTableRow = function (Package $package) {
    $packageId = $package->getPackageId();
    $cacheFile = rex_path::addonCache('cheatsheet', 'extension_points/' . $packageId . '.cache');
    $class = [];
    $count = '';
    $linkLabel = rex_i18n::msg('cheatsheet_extension_point_parse');
    if (file_exists($cacheFile)) {
        $class[] = 'text-muted';
        $cache = rex_file::getCache($cacheFile);
        $count = $cache == '' ? 0 : count($cache);
        $linkLabel = rex_i18n::msg('cheatsheet_extension_point_reparsing');

        if (count($package->getPlugins())) {
            /* @var $plugin \rex_plugin */
            foreach ($package->getPlugins() as $plugin) {
                $cacheFilePlugin = rex_path::addonCache('cheatsheet', 'extension_points/' . str_replace('/', '.', $plugin->getPackageId()) . '.cache');
                if (file_exists($cacheFilePlugin)) {
                    $cache = rex_file::getCache($cacheFilePlugin);
                    $count = $cache == '' ? $count : ($count + count($cache));
                }
            }
        }
    }

    return '
        <tr class="' . implode(' ', $class) . '">
            <td class="rex-table-icon"><i class="rex-icon fa fa-medkit"></i></td>
            <td data-title="' . \rex_i18n::msg('cheatsheet_extension_point_name') . '">' . $package->getName() . '</td>
            <td data-title="' . \rex_i18n::msg('cheatsheet_extension_point_amount') . '">' . $count . '</td>
            <td data-title="' . \rex_i18n::msg('cheatsheet_extension_point_function') . '"><a href="' .  rex_url::currentBackendPage(['func' => 'parse', 'package' => $packageId]). '">' . $linkLabel . '</a></td>
        </tr>' . "\n";
};

$getTable = function ($tableRows) {
    return '
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="rex-table-icon"></th>
                    <th>' . \rex_i18n::msg('cheatsheet_extension_point_name') . '</th>
                    <th class="rex-table-slim">' . \rex_i18n::msg('cheatsheet_extension_point_amount') . '</th>
                    <th class="rex-table-action">' . \rex_i18n::msg('cheatsheet_extension_point_function') . '</th>
                </tr>            
            </thead>
            <tbody>
                ' . $tableRows . '
            </tbody>
        </table>' . "\n";
};


$availableRows = '';
$bucketRows = '';

$availableRows .= $getTableRow(Package::get('core'));
foreach (rex_addon::getRegisteredAddons() as $addon) {
    $package = Package::get($addon->getPackageId());
    if ($addon->isAvailable()) {
        $availableRows .= $getTableRow($package);
    } else {
        $bucketRows .= $getTableRow($package);
    }
}

$fragment = new rex_fragment();
$fragment->setVar('title', \rex_i18n::msg('cheatsheet_extension_point_available_packages'));
$fragment->setVar('content', $getTable($availableRows), false);
$fragment->setVar('options', '<a class="btn btn-xs btn-default" href="' .  rex_url::currentBackendPage(['func' => 'parse', 'all' => 1]). '">' . \rex_i18n::msg('cheatsheet_extension_point_parse_all') . '</a>', false);
$content = $fragment->parse('core/page/section.php');
echo $content;

$fragment = new rex_fragment();
$fragment->setVar('title', \rex_i18n::msg('cheatsheet_extension_point_not_available_packages'));
$fragment->setVar('content', $getTable($bucketRows), false);
$content = $fragment->parse('core/page/section.php');
echo $content;
