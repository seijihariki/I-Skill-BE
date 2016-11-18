<?php
require_once('../../vendor/autoload.php');
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

$config = include('config.php');
$signer = new Sha256();

$dbhost = $config['dbhost'];
$dbport = $config['dbport'];
$dbname = $config['dbname'];
$dbuser = $config['dbuser'];
$dbpass = $config['dbpass'];

$dbconn = pg_connect("host=".$dbhost." port=".$dbport." dbname=".$dbname." user=".$dbuser." password=".$dbpass);

if ($dbconn == False)
{
    echo "{\"status\": \"error\", \"detail\": \"Failed to connect to DB.\"}";
    exit;
}

$jwttoken = $_POST["jwt"];
$search = $_POST["search"];
$itemcnt = $_POST["item_n"];
$filter = $_POST["filters"];
$area = $_POST["area"];

if(!$jwttoken->verify($signer, $config['jwtkey']))
{
    echo "{\"status\": \"invalid\", \"detail\": \"Invalid JWT\"}";
    exit;
}

echo "not error;";

?>
