<?php

require_once "vendor/autoload.php";
require_once "config.php";
$failed = false;
$success = false;

if (!isset($_GET['t']))
{
    echo "Invalid link";
    die;
}

use UnrealIRCd\Connection;

$rpc = new UnrealIRCd\Connection("wss://".UNREAL_RPC_HOSTNAME.":".UNREAL_RPC_HOSTPORT,
                    UNREAL_RPC_USERNAME.":".UNREAL_RPC_PASSWORD,
                    Array("tls_verify"=>FALSE));

if ($rpc->error)
{
    echo "Could not connect to UnrealIRCd (error code: $rpc->errno)";
    die;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recaptchaToken = $_POST['recaptcha_token'];
    $userIP = $_SERVER['REMOTE_ADDR'];

    // Post request to Google server for verification
    $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => CAPTCHA_SECRET_KEY,
        'response' => $recaptchaToken,
        'remoteip' => $userIP
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context  = stream_context_create($options);
    $response = file_get_contents($verifyUrl, false, $context);
    $responseKeys = json_decode($response, true);

    // Check if the verification was successful and assign a score
    if ($responseKeys["success"] && $responseKeys["score"] >= REQUIRED_SCORE) {
        $rpc->query("log.send", ["msg" => "Captcha completed [IP: ".$_SERVER['REMOTE_ADDR']."] [score: ".$responseKeys["score"]."]",
                                                "event_id" => "CAPTCHA_SUCCESS",
                                                "level" => "info",
                                                "subsystem" => "recaptcha"
                                                ]);
        $rpc->query("recaptcha.allow", ["token" => $_GET['t']]);
        $success = true;
    } else {
        $failed = $responseKeys["score"];
        $rpc->query("log.send", ["msg" => "Captcha failed [IP: ".$_SERVER['REMOTE_ADDR']."] [score: ".$responseKeys["score"]."]",
                                                "event_id" => "CAPTCHA_FAIL",
                                                "level" => "info",
                                                "subsystem" => "recaptcha"
                                                ]);
    }
}

?>
<!DOCTYPE html>
<html lang="tr">
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo CAPTCHA_SITE_KEY; ?>"></script>
<script>
    function onSubmit(token) {
        document.getElementById("myForm").submit();
    }

    grecaptcha.ready(function() {
        grecaptcha.execute('<?php echo CAPTCHA_SITE_KEY; ?>', {action: 'submit'}).then(function(token) {
            // Add the token to the form
            document.getElementById('recaptchaToken').value = token;
        });
    });
</script>
<style>
    body, body > div {
            margin: 0;
            height: 100vh; /* Full viewport height */
            display: flex;
            justify-content: center; /* Horizontal centering */
            align-items: center;     /* Vertical centering */
        }
</style>
<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo PAGE_TITLE; ?></title>
  <link rel="icon" type="image/png" href="favicon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">

 </head>
<body style="background-color: grey">
    <form action="" method="POST" id="myForm">
        <div class="card p-5">
            <!-- Google reCAPTCHA widget -->
            <?php if (!$failed && !$success) { ?>
            <p><?php echo PAGE_TEXT; ?></p>
            <input type="hidden" id="recaptchaToken" name="recaptcha_token">
            <button class="btn btn-primary" type="submit"><?php echo BUTTON_TEXT; ?></button>
            <?php }
            if ($success)
                echo SUCCESS_MESSAGE;
            ?>
        </div>
    </form>
</body>

</html>


