<?php

class HWAddRatingApi extends HWRatingsBaseApi {
  public function execute() {
    global $wgUser;
    if (!$wgUser->isAllowed('edit')) {
      $this->dieUsage("You don't have permission to add rating", "permissiondenied");
    }

    $params = $this->extractRequestParams();
    $page_id = $params['pageid'];
    $user_id = $wgUser->getId();
    $rating = $params['rating'];
    $timestamp = wfTimestampNow();

    // Exit with an error if pageid is not valid (eg. non-existent or deleted)
    $this->getTitleOrPageId($params);

    $dbw = wfGetDB( DB_MASTER );
    $dbw->upsert( // avoid duplicate entry for the same user
      'hw_ratings',
      array(
        'hw_user_id' => $user_id,
        'hw_page_id' => $page_id,
        'hw_rating' => $rating,
        'hw_timestamp' => $timestamp
      ),
      array('hw_user_id', 'hw_page_id'),
      array(
        'hw_rating' => $rating,
        'hw_timestamp' => $timestamp
      )
    );

    $aggregate = $this->updateRatingAverages($page_id);

    $this->getResult()->addValue('query' , 'average', round($aggregate['average'], 2));
    $this->getResult()->addValue('query' , 'count', intval($aggregate['count']));
    $this->getResult()->addValue('query' , 'pageid', intval($page_id));
    $this->getResult()->addValue('query' , 'timestamp', $timestamp);

    return true;
  }

  // Description
  public function getDescription() {
    return "Add/update user's rating of page";
  }

  // Parameters
  public function getAllowedParams() {
    global $wgHwRatingsMinRating, $wgHwRatingsMaxRating;
    return array(
      'pageid' => array (
        ApiBase::PARAM_TYPE => 'integer',
        ApiBase::PARAM_REQUIRED => true
      ),
      'rating' => array (
        ApiBase::PARAM_TYPE => 'integer',
        ApiBase::PARAM_REQUIRED => true,
        ApiBase::PARAM_MIN => $wgHwRatingsMinRating,
        ApiBase::PARAM_MAX => $wgHwRatingsMaxRating,
        ApiBase::PARAM_RANGE_ENFORCE => true
      ),
      'token' => array (
        ApiBase::PARAM_TYPE => 'string',
        ApiBase::PARAM_REQUIRED => true
      )
    );
  }

  // Describe the parameters
  public function getParamDescription() {
    global $wgHwRatingsMinRating, $wgHwRatingsMaxRating;
    return array_merge( parent::getParamDescription(), array(
      'rating' => 'Rating [' . $wgHwRatingsMinRating . '..' . $wgHwRatingsMaxRating . ']',
      'pageid' => 'Page id',
      'token' => 'csrf token'
    ) );
  }

  public function needsToken() {
    return 'csrf';
  }
}

?>
