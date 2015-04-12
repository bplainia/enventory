<?php
class Home implements page
{
  function title()
  {
    return "Home";
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