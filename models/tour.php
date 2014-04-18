<?php 

public class TourModel {
 
    /** 
     * Gets the contents of our cart, in a list of IDs
     */
    public function get() {
        return $_SESSION['tour'];
    }
 
    /** 
     * Adds a product ID to the cart
     */
    public function add($tour_id) {
        $_SESSION['tour'][] = $tour_id;
    }
 
    /** 
     * Removes a particular product ID from 
       the cart.
     */
    public function remove($tour_id) {
        foreach($_SESSION['tour'] as 
                $key => $_tour_id) {
            if ($productID == $_tour_id) {
                unset($_SESSION[$key]);
            }
        }

	public function getTourID() {

	}

	public function getTourVersionID() {

	}
	
	public function getAttributeValueObject($value, $bool = true) {
	
	}


}