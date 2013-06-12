<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Assets extends CI_Controller {

  function show($id)
  {
    $this->load->library('croomy_assets');
    $this->croomy_assets->get_asset($id);
  }

}