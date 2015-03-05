<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'services';
$app['version'] = '2.0.20';
$app['release'] = '1';
$app['vendor'] = 'Tim Burgess';
$app['packager'] = 'Tim Burgess';
$app['license'] = 'GPLv3';
$app['license_core'] = 'GPLv3';
$app['description'] = lang('services_app_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('services_app_name');
$app['category'] = lang('base_category_system');
$app['subcategory'] = lang('base_subcategory_settings');

/////////////////////////////////////////////////////////////////////////////
// Controllers
/////////////////////////////////////////////////////////////////////////////

$app['controllers']['services']['title'] = lang('services_app_name');

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['requires'] = array(
);

$app['core_requires'] = array(
   'app-base-core >= 1:1.6.5',
);

$app['core_file_manifest'] = array(
);

$app['core_directory_manifest'] = array(
);
