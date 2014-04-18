<?php
Loader::model('tour');  
class TourController extends Controller {
 
    private $tour;
 
    public function on_start() {
        $this->tour = new TourModel();
    }
  
}
?>