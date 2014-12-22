<?php
class HWAddRatingApi extends ApiBase {
  public function execute() {
    // Get parameters
    $params = $this->extractRequestParams();
    global $wgUser;

    $page_id = $params['pageid'];
    $user_id = $wgUser->getId();
    $rating = $params['rating'];
    $timestamp = wfTimestampNow();

    $pageObj = $this->getTitleOrPageId($params);
    if($rating > 0 && $rating <= 5) {
      $dbr = wfGetDB( DB_MASTER );

      $dbr->upsert(
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

      $res = $dbr->query("SELECT AVG(hw_rating) as average_rating, COUNT(hw_rating) as count_rating  FROM hw_ratings WHERE hw_page_id=".$dbr->addQuotes($page_id));
      $row = $res->fetchRow();
      $average = round($row['average_rating']);
      $count = $row['count_rating'];

      $dbr->upsert(
        'hw_ratings_avg',
        array(
          'hw_page_id' => $page_id,
          'hw_count_rating' => $count,
          'hw_average_rating' => $average
        ),
        array('hw_page_id'),
        array(
          'hw_page_id' => $page_id,
          'hw_count_rating' => $count,
          'hw_average_rating' => $average
        )
      );

      $this->getResult()->addValue('query' , 'average', $average);
      $this->getResult()->addValue('query' , 'count', $count);
      $this->getResult()->addValue('query' , 'pageid', $page_id);
    }
    else {
      $this->getResult()->addValue('error' , 'info', 'wating time should be positive.');
    }

    return true;
  }

  // Description
  public function getDescription() {
      return 'Add a rating to a spot.';
  }

  // Parameters.
  public function getAllowedParams() {
      return array(
          'rating' => array (
              ApiBase::PARAM_TYPE => 'string',
              ApiBase::PARAM_REQUIRED => true
          ),
          'pageid' => array (
              ApiBase::PARAM_TYPE => 'string',
              ApiBase::PARAM_REQUIRED => true
          ),
          'token' => array (
              ApiBase::PARAM_TYPE => 'string',
              ApiBase::PARAM_REQUIRED => true
          )
      );
  }

  // Describe the parameter
  public function getParamDescription() {
      return array_merge( parent::getParamDescription(), array(
          'rating' => 'Rating to add to the spot',
          'pageid' => 'Id of the spot to rate',
          'token' => 'User edit token'
      ) );
  }

  public function needsToken() {
      return 'csrf';
  }

}
