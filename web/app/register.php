<?php
require_once('../../vendor/autoload.php');

$dbhost = 'ec2-54-221-253-87.compute-1.amazonaws.com';
$dbport = '5432';
$dbname = 'db2bq0vfsn68vn';
$dbuser = 'odfehicdceyspg';
$dbpass = 'GA70wAn9eSONOLUVk9Iihs04U5';

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

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

$userqr  = "SELECT user, email FROM users WHERE username = ".$username." OR email = ".$email.";";

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
    $salt = generateRandomString();
    $passhash = hash('sha256', $password.$salt);
    $userins = "INSERT INTO users VALUES (\"".$username."\", \"".$fullname."\", \"".$email."\", \"".$passhash."\", \"".$salt.");";
    $res = pg_query($dbconn, $userins);
    if ($res == false)
    {
        echo "{status: \"error\", detail: \"Failed to register user into DB.\"}";
    } else {
        echo "{status: \"success\"}";
    }
    exit;
}

exit("This shouldn't have been reached...");
?>
