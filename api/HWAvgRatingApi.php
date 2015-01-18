<?php

class HWAvgRatingApi extends HWRatingsBaseApi {
  public function execute() {
    $params = $this->extractRequestParams();
    $page_ids = $params['pageid'];
    if ($params['user_id'])
      $user_id = $params['user_id'];
    else
      $user_id = 0;

    // Basic query settings
    $tables = array('hw_ratings_avg');
    $fields = array(
      'hw_average_rating',
      'hw_count_rating',
      'hw_ratings_avg.hw_page_id'
    );
    $join_conds = array();

    // Join user's rating from hw_ratings if user_id was specified
    if ($user_id) {
      $tables[] = 'hw_ratings';
      $fields[] = 'COALESCE(hw_ratings.hw_rating, -1) AS user_rating';
      $fields[] = "COALESCE(hw_ratings.hw_timestamp, '') AS user_timestamp";
      $join_conds['hw_ratings'] = array( 'LEFT JOIN', array(
        'hw_ratings.hw_page_id = hw_ratings_avg.hw_page_id',
        'hw_ratings.hw_user_id = ' . $user_id
      ) );
    }

    $dbr = wfGetDB( DB_SLAVE );
    $res = $dbr->select(
      $tables,
      $fields,
      array(
        'hw_ratings_avg.hw_page_id' => $page_ids
      ),
      __METHOD__,
      array(),
      $join_conds
    );

    $this->getResult()->addValue( array( 'query' ), 'ratings', array() );
    foreach( $res as $row ) {
      $vals = array(
        'pageid' => intval($row->hw_page_id),
        'rating_average' => round($row->hw_average_rating, 2),
        'rating_count' => intval($row->hw_count_rating)
      );
      if ($user_id) {
        $vals['rating_user'] = intval($row->user_rating);
        $vals['timestamp_user'] = $row->user_timestamp;
      }
      $this->getResult()->addValue( array( 'query', 'ratings' ), null, $vals );
    }

    return true;
  }

  // Description
  public function getDescription() {
      return 'Get rating count and average rating of one or more pages';
  }

  // Parameters
  public function getAllowedParams() {
    return array(
      'pageid' => array (
        ApiBase::PARAM_TYPE => 'integer',
        ApiBase::PARAM_REQUIRED => true,
        ApiBase::PARAM_ISMULTI => true
      ),
      'user_id' => array (
        ApiBase::PARAM_TYPE => 'integer',
        ApiBase::PARAM_REQUIRED => false
      )
    );
  }

  // Describe the parameters
  public function getParamDescription() {
      return array_merge( parent::getParamDescription(), array(
          'pageid' => 'Page ids, delimited by | (vertical bar)',
          'user_id' => "Optional user id to get specific user's rating"
      ) );
  }
}

?>
