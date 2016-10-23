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

$email    = $_POST["email"];
$fullname = $_POST["name"];
$username = $_POST["user"];
$password = $_POST["pass"];

// Username sanity checking

$userqr  = "SELECT user, email FROM users WHERE user = ".$username." OR email = ".$email.";";

$userrec = pg_query($dbconn, $userqr);

if ($userrec)
{
    if (pg_num_rows($userrec) == 1)
    {
        $row = pg_fetch_row($saltrec);
        $user = $row[0];
        $mail = $row[1];
        if ($user == $username)
        {
            echo "{status: \"exists\", detail: \"Username already exists\"}";
            exit;
        } else {
            echo "{status: \"exists\", detail: \"Email is already being used\"}";
            exit;
        }
    } else {
        echo "{status: \"error\", detail: \"More than one entry found\"}";
        exit;
    }
} else {
    // Insert data into DB
    echo "{status: \"success\"}";
    exit;
}

exit("This shouldn't have been reached...");
?>
