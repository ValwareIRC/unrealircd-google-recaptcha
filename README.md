# UnrealIRCd Google reCAPTCHA Verification Page
### _Please make sure you have read and installed the prerequisites from the [module documentation page](https://github.com/ValwareIRC/valware-unrealircd-mods/blob/main/google-recaptcha/google-recaptcha.md)_
The verification page for UnrealIRCd Google reCAPTCHA Module

<img src="https://i.ibb.co/r5c0sw1/Screenshot-from-2024-10-16-08-50-11.png" style="width:500px;height:250px">

## Installation
A rough guide to install which assumes you want to install into `/var/www/html/verify` (`https://example.com/verify`) and your webserver user is `www-data` on Ubuntu/Debian:
```
cd /var/www/html/
git clone https://github.com/ValwareIRC/unrealircd-google-recaptcha verify
cd verify
sudo -u www-data composer install
```

### Configuration
Just modify the `config.php` file to suit your needs, nothing complicated!
