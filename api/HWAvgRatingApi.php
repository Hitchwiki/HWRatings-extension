<?php
class HWAvgRatingApi extends ApiBase {
  public function execute() {
    // Get parameters
    $params = $this->extractRequestParams();

    $page_id = $params['pageid'];
    $pageObj = $this->getTitleOrPageId($params);

    $dbr = wfGetDB( DB_SLAVE );
    $res = $dbr->select(
      'hw_ratings_avg',
      array(
        'hw_average_rating',
        'hw_count_rating',
        'hw_page_id'
      ),
      'hw_page_id IN ('.$page_id.')'
    );
    foreach( $res as $row ) {
      $vals = array(
        'pageid' => $row->hw_page_id,
        'rating_average' => $row->hw_average_rating,
        'rating_count' => $row->hw_count_rating
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
              ApiBase::PARAM_TYPE => 'string',
              ApiBase::PARAM_REQUIRED => true
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
