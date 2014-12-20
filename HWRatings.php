<?php

$wgExtensionCredits['api'][] = array(
	'path' => __FILE__,
	'name' => 'HWRatings',
	'version' => '0.0.1',
	"authors" => "http://hitchwiki.org"
);

$dir = __DIR__;

//Database hook
$wgAutoloadClasses['HWRatingsHooks'] = "$dir/HWRatingsHooks.php";
$wgHooks['LoadExtensionSchemaUpdates'][] = 'HWRatingsHooks::onLoadExtensionSchemaUpdates';

//APIs
$wgAutoloadClasses['HWAddRatingApi'] = "$dir/api/HWAddRatingApi.php";
$wgAPIModules['hwaddrating'] = 'HWAddRatingApi';

return true;
