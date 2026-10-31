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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $turnstileToken = $_POST['cf-turnstile-response'] ?? '';
    $userIP = $_SERVER['REMOTE_ADDR'] ?? '';

    $success = false;
    $failed = false;

    // Reject missing or oversized tokens
    if (!is_string($turnstileToken) ||
        $turnstileToken === '' ||
        strlen($turnstileToken) > 2048) {

        $failed = true;
    } else {
        $verifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

        $data = [
            'secret'   => TURNSTILE_SECRET_KEY,
            'response' => $turnstileToken,
            'remoteip' => $userIP
        ];

        $options = [
            'http' => [
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
                'timeout' => 10,
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($options);
        $response = @file_get_contents($verifyUrl, false, $context);

        $responseKeys = ($response !== false)
            ? json_decode($response, true)
            : null;

        if (is_array($responseKeys) &&
            ($responseKeys['success'] ?? false) === true) {

            $rpc->query("log.send", [
                "msg" => "Captcha completed [IP: " . $userIP . "]",
                "event_id" => "CAPTCHA_SUCCESS",
                "level" => "info",
                "subsystem" => "turnstile"
            ]);

            $rpc->query("recaptcha.allow", [
                "token" => $_GET['t']
            ]);

            $success = true;

        } else {
            $failed = true;

            $rpc->query("log.send", [
                "msg" => "Captcha failed [IP: " . $userIP . "]",
                "event_id" => "CAPTCHA_FAIL",
                "level" => "info",
                "subsystem" => "turnstile"
            ]);
        }
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
<script
    src="https://challenges.cloudflare.com/turnstile/v0/api.js"
    async
    defer
></script>
<style>
    body, body > div {
            margin: 0;
            height: 100vh; /* Full viewport height */
            display: flex;
            justify-content: center; /* Horizontal centering */
            align-items: center;     /* Vertical centering */
        }
</style>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo PAGE_TITLE; ?></title>
  <link rel="icon" type="image/png" href="favicon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">

 </head>
<body style="background-color: grey">
<form action="" method="POST" id="myForm">
    <div class="card p-5">

        <?php if (!$failed && !$success) { ?>

            <p><?php echo PAGE_TEXT; ?></p>

            <div
                class="cf-turnstile"
                data-sitekey="<?php echo htmlspecialchars(
                    TURNSTILE_SITE_KEY,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
            ></div>

            <button
                class="btn btn-primary mt-3"
                type="submit"
            >
                <?php echo BUTTON_TEXT; ?>
            </button>

        <?php } ?>

        <?php if ($success) { ?>
            <?php echo SUCCESS_MESSAGE; ?>
        <?php } ?>

        <?php if ($failed) { ?>
            <p class="text-danger">
                Verification failed. Please try again.
            </p>

            <a href="" class="btn btn-secondary">
                Retry
            </a>
        <?php } ?>

    </div>
</form>
</body>

</html>


