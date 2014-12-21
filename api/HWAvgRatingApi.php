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
      'hw_average_rating',
      'hw_page_id ='.$page_id
    );
    $row = $res->fetchRow();
    $average = $row[0];

    if($row) {
      $this->getResult()->addValue('query' , 'average', $average);
      $this->getResult()->addValue('query' , 'pageid', $page_id);
    }
    else {
      $this->getResult()->addValue('error' , 'info', 'No average rating for this page.');
    }

    return true;
  }

  // Description
  public function getDescription() {
      return 'Get average rating of a page.';
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
