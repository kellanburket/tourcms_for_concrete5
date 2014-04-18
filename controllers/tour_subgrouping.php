<?php
Loader::model('tour_subgrouping');  
class TourSubgroupingController extends Controller {
 
    private $tour;
 
    public function on_start() {
        $this->tour = new TourSubgroupingModel();
    }
  
}
?>