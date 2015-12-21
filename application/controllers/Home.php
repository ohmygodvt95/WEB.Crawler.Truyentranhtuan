<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function index()
    {
        $date=date_create("2.4.2015");
        echo date_format($date,"d-m-Y");
    }

    public function truyen()
    {
        $this->load->view('home/truyen');
    }

}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */