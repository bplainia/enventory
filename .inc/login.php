<?php
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    if($_SESSION['authAttempt']++ < 3) 
    {
        header('WWW-Authenticate: Basic realm="eNVENTORY"');
        header('HTTP/1.0 401 Unauthorized');
    }
    echo 'You have reached the maximum number of';
    exit;
} else {
    if(authed){
      $_SESSION['authAttempt'] = 0;
      echo "<p>Hello {$_SERVER['PHP_AUTH_USER']}.</p>";
      echo "<p>You entered {$_SERVER['PHP_AUTH_PW']} as your password.</p>";
    }
}