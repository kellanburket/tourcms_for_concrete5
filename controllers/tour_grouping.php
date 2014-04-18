<?php
Loader::model('tour_grouping');  
class TourGroupingController extends Controller {
 
    private $tour;
 
    public function on_start() {
        $this->tour = new TourGroupingModel();
    }
  
}
?>