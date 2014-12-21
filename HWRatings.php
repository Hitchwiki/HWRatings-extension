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
$wgAutoloadClasses['HWAvgRatingApi'] = "$dir/api/HWAvgRatingApi.php";
$wgAPIModules['hwaddrating'] = 'HWAddRatingApi';
$wgAPIModules['hwavgrating'] = 'HWAvgRatingApi';

return true;
