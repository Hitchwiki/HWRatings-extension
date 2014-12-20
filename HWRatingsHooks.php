<?php

class HWRatingsHooks {

  public static function onLoadExtensionSchemaUpdates( DatabaseUpdater $updater ) {
    $updater->addExtensionTable( 'hw_ratings', dirname( __FILE__ ) . '/sql/db-hw_ratings.sql' );
    $updater->addExtensionTable( 'hw_ratings_avg', dirname( __FILE__ ) . '/sql/db-hw_ratings_avg.sql' );

    return true;
  }
}



