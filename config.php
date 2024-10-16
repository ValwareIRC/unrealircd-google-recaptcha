<?php

// UnrealIRCd's RPC Credentials:
define('UNREAL_RPC_HOSTNAME', "127.0.0.1");
define('UNREAL_RPC_HOSTPORT', "8001");
define('UNREAL_RPC_USERNAME', "rpc_username");
define('UNREAL_RPC_PASSWORD', "secret_password");

// Google reCAPTCHA
define('CAPTCHA_SITE_KEY', "abc123");
define('CAPTCHA_SECRET_KEY', "abc123");

// Page Customization
define('PAGE_TITLE', "Verification Required");
define('PAGE_TEXT', "Please verify you're human by clicking the button below.<br><input required type='checkbox'> I agree to the <a href='#'>Terms and Conditions</a></input>");
define('BUTTON_TEXT', "I am human!");
define('SUCCESS_MESSAGE', "Thank you so much! You may now close this page and continue in IRC.");

// How high a score is needed to be human
define('REQUIRED_SCORE', "0.5");
