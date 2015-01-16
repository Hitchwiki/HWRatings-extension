<?php
class HWGetRatingsApi extends ApiBase {
  public function execute() {
    // Get parameters
    $params = $this->extractRequestParams();

    $page_id = $params['pageid'];
    $pageObj = $this->getTitleOrPageId($params);

    $dbr = wfGetDB( DB_SLAVE );
    $res = $dbr->select(
      array(
        'hw_ratings',
        'user'
      ),
      array(
        'hw_user_id',
        'hw_page_id',
        'hw_rating',
        'hw_timestamp',
        'user_name'
      ),
      'hw_page_id ='.$page_id,
      __METHOD__,
      array(),
      array( 'user' => array( 'JOIN', array(
        'hw_ratings.hw_user_id = user.user_id',
      ) ) )
    );

    foreach( $res as $row ) {
      $vals = array(
        'pageid' => $row->hw_page_id,
        'user_id' => $row->hw_user_id,
        'rating' => $row->hw_rating,
        'timestamp' => $row->hw_timestamp,
        'user_name' => $row->user_name
      );
      $this->getResult()->addValue( array( 'query', 'ratings' ), null, $vals );
    }
    if($vals == null) {
        $this->getResult()->addValue( array( 'query', 'ratings' ), null, null);
    }

    return true;
  }

  // Description
  public function getDescription() {
      return 'Get all the ratings of a page.';
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
          'pageid' => 'Id of the page',
      ) );
  }
}
