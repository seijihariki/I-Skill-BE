<?php
require_once('vendor/autoload.php');

$dbhost = 'ec2-54-221-253-87.compute-1.amazonaws.comOC';
$dbport = 5432;
$dbname = 'db2bq0vfsn68vn';
$dbuser = 'odfehicdceyspg';
$dppass = ' GA70wAn9eSONOLUVk9Iihs04U5';

$dbconn = pg_connect("host=".$dbhost." port=".$dbport." dbname=".$dbname." user=".$dbuser." password=".$dbpass);

?>
