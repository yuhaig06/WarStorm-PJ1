<?php
namespace App\Controllers;

class EventsController {
    public function index() {
        require APPROOT . '/app/views/sidebar/events/eventad.php';
    }
}
