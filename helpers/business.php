<?php 

function get_logo($business) {
    if(isset($business["logo"]) && $business["logo"]) {
        return BASE_URL . $business["logo"];
    } else {
        return BASE_URL . '/assets/img/business.png';
    }
}