<?php
require_once('../../vendor/autoload.php');

$dbhost = 'ec2-54-221-253-87.compute-1.amazonaws.com';
$dbport = '5432';
$dbname = 'db2bq0vfsn68vn';
$dbuser = 'odfehicdceyspg';
$dbpass = 'GA70wAn9eSONOLUVk9Iihs04U5';

$dbconn = pg_connect("host=".$dbhost." port=".$dbport." dbname=".$dbname." user=".$dbuser." password=".$dbpass);

if ($dbconn == False)
{
    echo "{status: \"error\", detail: \"Failed to connect to DB.\"}";
    exit;
}

$username = $_POST["user"];
$password = $_POST["pass"];

// Username sanity checking

$saltqr  = "SELECT pass, salt FROM users WHERE username = \"".$username."\" OR email = \"".$username."\";";
$saltrec = pg_query($dbconn, $saltqr);

if ($saltrec)
{
    if (pg_num_rows($saltrec) == 1)
    {
        $row = pg_fetch_row($saltrec);
        $expe = $row[0];
        $salt = $row[1];
        $hash = hash('sha256', $password.$salt);
        if ($expe == $hash)
        {
            // Create session and jwt token
        }
    } else {
        echo "{status: \"error\", detail: \"More than one entry found\"}";
        exit;
    }
} else {
    echo "{status: \"wrong\", detail: \"Wrong username or password\"}";
    exit;
}

exit("This shouldn't have been reached...");
?>
