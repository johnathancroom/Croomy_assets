<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Croomy_assets {

  function __construct() {
    $this->ci =& get_instance();
    $this->ci->config->load('croomy_assets');
    $this->assets = $this->ci->config->item('croomy_assets');
  }

  function get_path($name)
  {
    $asset = $this->assets[$name];

    return '/assets/show/'.$name.'-'.$this->get_md5($asset['files']).'.'.$asset['extension'];
  }

  function get_asset($id)
  {
    $id = explode('.', $id);
    $name_with_md5 = explode('-', $id[0]);
    $name = $name_with_md5[0];
    $ext = $id[1];

    $asset = $this->assets[$name];

    $assets = $asset['files'];

    $mtimes = array();

    foreach($assets as $a)
    {
      $mtimes[] = filemtime($a);
    }

    $this->ci->output->set_content_type($ext);
    $this->ci->output->set_header('Last-Modified: '.gmdate('D, d M Y H:i:s', max($mtimes)).' GMT');
    $this->ci->output->set_header('Expires: '.gmdate('D, d M Y H:i:s', (max($mtimes) + 2592000)).' GMT');

    $merged = '';

    foreach($assets as $a)
    {
      $merged .= $this->file_include_contents($a);
    }

    /* Compress and remove comments from CSS */
    if($asset['extension'] == 'css')
    {
      $merged = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $merged);
      $merged = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $merged);
    }

    $this->ci->output->set_output($merged);
  }

  private function file_include_contents($filename)
  {
    if(is_file($filename))
    {
      ob_start();
      include $filename;
      return ob_get_clean();
    }

    return false;
  }

  private function get_md5($files)
  {
    $compilation = '';

    foreach($files as $file)
    {
      $compilation .= md5_file($file);
    }

    return md5($compilation);
  }

}