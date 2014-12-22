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
        'hw_count_rating'
      ),
      'hw_page_id ='.$page_id
    );
    $row = $res->fetchRow();
    $average = $row['hw_average_rating'];
    $count = $row['hw_count_rating'];

    if($row) {
      $this->getResult()->addValue('query' , 'average', $average);
      $this->getResult()->addValue('query' , 'count', $count);
      $this->getResult()->addValue('query' , 'pageid', $page_id);
    }
    else {
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
