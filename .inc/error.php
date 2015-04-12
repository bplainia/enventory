<?php
class Error implements page
{
  function __construct()
  {
    if(isset($_GET['errno']) && ctype_digit($_GET['errno']))
    {
      switch($_GET['errno'])
      {
      case 404:  
        header('HTTP/1.0 404 Not Found');
        $this->title = "Page not found";
        break;
      case 403:
        header('HTTP/1.0 403 Forbidden');
        $this->title = "Access denied";
        break;
      default: // no error occurred. somebody is just trying to play with me. :(
        header('location:/');
        die();
      }
    }
    elseif(!isset($_GET['errno'])) // no error occurred. somebody is just trying to play with me. :(
    {
      header('location:/');
      die();
    }
  }
  function title()
  {
    return $this->title;
  }
  
  function content()
  {
  $content = <<<EOD
  <h1>Home</h1>
  <p>Please select a option on the top bar to begin!</p>
EOD;
    return $content;
  }
  
  function jscript()
  {
    return "";
  }
}