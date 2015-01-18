<?php

$wgExtensionCredits['HWRatings'][] = array(
	'path' => __FILE__,
	'name' => 'HWRatings',
	'version' => '0.0.1',
	"authors" => "http://hitchwiki.org"
);

$dir = __DIR__;

//Database hook
$wgAutoloadClasses['HWRatingsHooks'] = "$dir/HWRatingsHooks.php";
$wgHooks['LoadExtensionSchemaUpdates'][] = 'HWRatingsHooks::onLoadExtensionSchemaUpdates';

//Deletion and undeletion hooks
$wgHooks['ArticleDeleteComplete'][] = 'HWRatingsHooks::onArticleDeleteComplete';
$wgHooks['ArticleRevisionUndeleted'][] = 'HWRatingsHooks::onArticleRevisionUndeleted';

//APIs
$wgAutoloadClasses['HWRatingsBaseApi'] = "$dir/api/HWRatingsBaseApi.php";
$wgAutoloadClasses['HWAddRatingApi'] = "$dir/api/HWAddRatingApi.php";
$wgAutoloadClasses['HWDeleteRatingApi'] = "$dir/api/HWDeleteRatingApi.php";
$wgAutoloadClasses['HWAvgRatingApi'] = "$dir/api/HWAvgRatingApi.php";
$wgAutoloadClasses['HWGetRatingsApi'] = "$dir/api/HWGetRatingsApi.php";
$wgAPIModules['hwaddrating'] = 'HWAddRatingApi';
$wgAPIModules['hwdeleterating'] = 'HWDeleteRatingApi';
$wgAPIModules['hwavgrating'] = 'HWAvgRatingApi';
$wgAPIModules['hwgetratings'] = 'HWGetRatingsApi';

return true;
