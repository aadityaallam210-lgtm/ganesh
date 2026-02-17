<?php
define("server", "localhost");

define ("username", "root");

define("password", "");

define ("dbname", "ganesh");

$conn = mysql_connect(server, username, password) OR die(mysql_error()); 
mysql_select_db(dbname,$conn) OR die (mysql_error());
?>