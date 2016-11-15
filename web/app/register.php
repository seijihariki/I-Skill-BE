<?php
require_once('../../vendor/autoload.php');

$config = include('config.php');

$dbhost = $config['dbhost'];
$dbport = $config['dbport'];
$dbname = $config['dbname'];
$dbuser = $config['dbuser'];
$dbpass = $config['dbpass'];

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

// Input sanity checking

$userqr  = "SELECT user, email FROM users WHERE username = '".$username."' OR email = '".$email."';";

$userrec = pg_query($dbconn, $userqr);

if ($userrec)
{
    $rcnt = pg_num_rows($userrec);
    if ($rcnt == 1)
    {
        $row = pg_fetch_row($saltrec);
        $user = $row[0];
        $mail = $row[1];
        if ($user == $username)
        {
            echo "{status: \"exists\", detail: \"Username already exists\"}";
        } else {
            echo "{status: \"exists\", detail: \"Email is already being used\"}";
        }
    }
    if ($rcnt > 1)
    {
        echo "{status: \"error\", detail: \"More than one entry found\"}";
    }
    if ($rcnt == 0)
    {
        $salt = generateRandomString();
        $passhash = hash('sha256', $password.$salt);
        $userins = "INSERT INTO users (username, name, email, pass, salt) VALUES ('".$username."', '".$fullname."', '".$email."', '".$passhash."', '".$salt."');";
        $res = pg_query($dbconn, $userins);

        if ($res == false)
        {
            echo "{status: \"error\", detail: \"Failed to register user into DB.\"}";
        } else {
            echo "{status: \"success\"}";
        }
    }
    exit;
} else {
    echo "{status: \"error\", detail: \"Query for user returned error.\"}";
}

exit("This shouldn't have been reached...");
?>
