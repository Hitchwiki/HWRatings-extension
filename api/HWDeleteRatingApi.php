<?php

class HWDeleteRatingApi extends ApiBase {
  public function execute() {
    global $wgUser;
    if (!$wgUser->isAllowed('edit')) {
      $this->dieUsage("You don't have permission to block users", "permissiondenied");
    }

    $params = $this->extractRequestParams();
    $page_id = $params['pageid'];
    $user_id = $wgUser->getId();

    // Exit with an error if pageid is not valid (eg. non-existent or deleted)
    $this->getTitleOrPageId($params);

    $dbw = wfGetDB( DB_MASTER );
    $dbw->delete(
      'hw_ratings',
      array(
        'hw_user_id' => $user_id,
        'hw_page_id' => $page_id
      )
    );

    // Get fresh rating count and average rating
    $res = $dbw->select(
      'hw_ratings',
      array(
        'COALESCE(AVG(hw_rating), 0) AS average_rating', // we decided to stay away from NULLs
        'COUNT(*) AS count_rating'
      ),
      array(
        'hw_page_id' => $page_id
      )
    );
    $row = $res->fetchRow();
    $average = $row['average_rating'];
    $count = $row['count_rating'];

    // Update rating count and average rating cache
    $dbw->upsert(
      'hw_ratings_avg',
      array(
        'hw_page_id' => $page_id,
        'hw_count_rating' => $count,
        'hw_average_rating' => $average
      ),
      array('hw_page_id'),
      array(
        'hw_count_rating' => $count,
        'hw_average_rating' => $average
      )
    );

    $this->getResult()->addValue('query' , 'average', round($average, 2));
    $this->getResult()->addValue('query' , 'count', intval($count));
    $this->getResult()->addValue('query' , 'pageid', intval($page_id));

    return true;
  }

  // Description
  public function getDescription() {
    return "Delete user's rating of a page";
  }

  // Parameters
  public function getAllowedParams() {
    return array(
      'pageid' => array (
        ApiBase::PARAM_TYPE => 'integer',
        ApiBase::PARAM_REQUIRED => true
      ),
      'token' => array (
        ApiBase::PARAM_TYPE => 'string',
        ApiBase::PARAM_REQUIRED => true
      )
    );
  }

  // Describe the parameters
  public function getParamDescription() {
    return array_merge( parent::getParamDescription(), array(
      'pageid' => 'Page id',
      'token' => 'csrf token'
    ) );
  }

  public function needsToken() {
      return 'csrf';
  }

}

?>