<?php

// UnrealIRCd's RPC Credentials:
define('UNREAL_RPC_HOSTNAME', "127.0.0.1");
define('UNREAL_RPC_HOSTPORT', "8001");
define('UNREAL_RPC_USERNAME', "rpc_username");
define('UNREAL_RPC_PASSWORD', "secret_password");

// Google reCAPTCHA
define('CAPTCHA_SITE_KEY', "abc123");
define('CAPTCHA_SECRET_KEY', "abc123");
// or Cloudflare Turnstile
define('TURNSTILE_SITE_KEY', 'abc123');
define('TURNSTILE_SECRET_KEY', 'abc123');

// Page Customization
define('PAGE_TITLE', "Verification Required");
define('PAGE_TEXT', "Please verify you're human by clicking the button below.<br><input required type='checkbox'> I agree to the <a href='#'>Terms and Conditions</a></input>");
define('BUTTON_TEXT', "I am human!");
define('SUCCESS_MESSAGE', "Thank you so much! You may now close this page and continue in IRC.");

/*
// French
define('PAGE_TITLE', "Vérification requise");
define('PAGE_TEXT', "Veuillez confirmer que vous êtes un humain en cliquant sur le bouton ci-dessous.<br><input required type='checkbox'> J'accepte les <a href='#' target='_blank'>Conditions générales</a></input>");
define('BUTTON_TEXT', "Je suis humain !");
define('SUCCESS_MESSAGE', "Merci beaucoup ! Vous pouvez maintenant fermer cette page et continuer sur IRC.");
*/

// How high a score is needed to be human
define('REQUIRED_SCORE', "0.5");