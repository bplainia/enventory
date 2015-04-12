<?php
ob_start();

interface page {                                                                // the page interface
  function title();                                                             // returns the title of the page
  function content();                                                           // returns the entire page content from template
  function jscript();                                                           // returns javascript for the page
}

interface component extends page                                                // the component interface
{
  function table();                                                             // outputs the table of components as a html string
  function form();                                                              // outputs the form for adding a component as a string
  function addComponent($data);                                                 // add component command
  function useComponent($id, $data);                                            // use a component command
  function delComponent($id);                                                   // delete a component command
  function modComponent($id, $data);                                            // modify a component command
}

function startsql(){                                                            // start a database connection
  require ".inc/config.php";  // import configuration
  $db = new PDO("$db_type:host=$db_host;dbname=$db_name",$db_user,$db_pass);    // initialize DB connection
  $db->query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");                     // set UTF8 as character set
  $db->query('SET CHARACTER SET utf8');
  return $db;                                                                   // function returns the PDO class
}
$page = "";                                                                     // initialize for good code
if(isset($_GET['page']))                                                        // if page is defined instantiate that page, otherwise instantiate index page
{
  $page = stripslashes($_GET['page']);                                          // remove slashes and create the page variable
  if(ctype_alnum($page) && file_exists(".inc/$page.php"))                       // if the file referred to exists (and is alpha-numeric only)...
  {
    try
    {
      require ".inc/$page.php";                                                 // import the "page" (we don't know if it is a page yet)
    }
    catch(Exception $e) {
      echo "there is a problem with the file";
    }
    finally {
      ob_end_clean();
    }
  }
  else
  {
    header('HTTP/1.0 404 Not Found');                                           // otherwise give a 404 error (could do custom error)
    die("404: that page wasn't found 1");                                       // Man down!
  }
}
else                                                                            // if page GET variable not set, import the home page
{
  $page = "home";
  require ".inc/home.php";
} 

if(class_exists($page))                                                         // if the class named by the page exists...
{
  $page = ucfirst($page);                                                       // make sure first letter is capitalized (classes all start with capital letter)
  $pageObj = new $page();                                                       // instantiate the class
  if(!($pageObj instanceof page))                                               // if the class is not a page (otherwise it is a page)...
  {
    header('HTTP/1.0 404 Not Found');                                           // give a 404 error (could do custom error)
    die("404: that page wasn't found 2");                                       // Man down!
  }
}
else                                                                            // since there is no class...
{
  header('HTTP/1.0 404 Not Found');                                             // give a 404 error (could do custom error)
  die("404: that page wasn't found 3");                                         // Man down!
}

session_start();                                                                // start the session

require ".inc/header.php";                                                      // now to import the header template (also contains footer)
$errorBuff = ob_get_clean();
echo $header;
echo $pageObj->content();
if($pageObj instanceof component) echo $pageObj->table();
if($pageObj instanceof component) echo $pageObj->form();
if($pageObj instanceof component) echo json_encode($pageObj->components);
if(strlen($errorBuff) > 0)
echo <<<EOD
<p>There were errors in creation</p>
<div style="display:hidden;" id="errorBuff">
$errorBuff
</div>
EOD;
echo $footer;

// end of script
