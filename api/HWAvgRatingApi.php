<?php
class HWAvgRatingApi extends ApiBase {
  public function execute() {
    global $wgUser;

    // Get parameters
    $params = $this->extractRequestParams();

    $page_ids = $params['pageid'];
    $pageObj = $this->getTitleOrPageId($params);

    if ($params['user_id'])
      $user_id = $params['user_id'];
    else
      $user_id = $wgUser->getId();

    $dbr = wfGetDB( DB_SLAVE );
    $res = $dbr->select(
      array('hw_ratings_avg', 'hw_ratings'),
      array(
        'hw_average_rating',
        'hw_count_rating',
        'hw_ratings_avg.hw_page_id',
        'hw_ratings.hw_rating'
      ),
      'hw_ratings_avg.hw_page_id IN ('.implode(',', $page_ids).')',
      __METHOD__,
      array(),
      array( 'hw_ratings' => array( 'LEFT JOIN', array(
        'hw_ratings.hw_page_id = hw_ratings_avg.hw_page_id',
        'hw_ratings.hw_user_id = ' . $user_id
      ) ) )
    );
    foreach( $res as $row ) {
      $vals = array(
        'pageid' => $row->hw_page_id,
        'rating_average' => $row->hw_average_rating,
        'rating_count' => $row->hw_count_rating,
        'rating_user' => $row->hw_rating ? $row->hw_rating : "0"
      );
      $this->getResult()->addValue( array( 'query', 'ratings' ), null, $vals );
    }
    if($vals == null) {
        $this->getResult()->addValue('error' , 'info', 'No average rating for this page.');
    }
    return true;
  }

  // Description
  public function getDescription() {
      return 'Get average rating and rating count of a page.';
  }

  // Parameters.
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

  // Describe the parameter
  public function getParamDescription() {
      return array_merge( parent::getParamDescription(), array(
          'pageid' => 'Id of the spot to rate',
      ) );
  }
}
