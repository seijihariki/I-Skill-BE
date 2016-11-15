<?php
require_once('../../vendor/autoload.php');

$config = include('config.php');

$dbhost = $config['dbhost'];
$dbport = $config['dbport'];
$dbname = $config['dbname'];
$dbuser = $config['dbuser'];
$dbpass = $config['dbpass'];

$jwtconfig = new Configuration();

$dbconn = pg_connect("host=".$dbhost." port=".$dbport." dbname=".$dbname." user=".$dbuser." password=".$dbpass);

if ($dbconn == False)
{
    echo "{status: \"error\", detail: \"Failed to connect to DB.\"}";
    exit;
}

$username = $_POST["user"];
$password = $_POST["pass"];

// Username sanity checking

$saltqr  = "SELECT id, username, pass, salt FROM users WHERE username = '".$username."' OR email = '".$username."';";
$saltrec = pg_query($dbconn, $saltqr);

if ($saltrec)
{
    if (pg_num_rows($saltrec) == 1)
    {
        $row = pg_fetch_row($saltrec);
        $expe = $row[2];
        $salt = $row[3];
        $hash = hash('sha256', $password.$salt);
        if ($expe == $hash)
        {
            $tokenID = base64_encode(mcrypt_create_iv(32));
            $issueTime = time();
            $expire = $issueTime + $config['extime'];
            $issuer = $config['issuer'];

            // Create session and jwt token
            $JWTKey = base64_decode($config['jwtkey']);
            $token = $jwtconfig->createBuilder()
                ->issuedBy($issuer)
                ->canOnlyBeUsedBy($issuer)
                ->identifiedBy($tokenID)
                ->issuedAt($issueTime)
                ->canOnlyBeUsedAfter($issueTime)
                ->expiresAt($expire)
                ->with('uid', $row[0])
                ->with('username', $row[1])
                ->getToken();
            echo "CREATED";
            echo "{status: \"OK\", jwt: \"".$token."\"}";
            exit;
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
