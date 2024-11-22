<?php

require_once "./plugins/TwoFactorAuth-3.0.2/lib/TwoFactorAuth.php";
require_once "./plugins/TwoFactorAuth-3.0.2/lib/TwoFactorAuthException.php";
require_once "./plugins/TwoFactorAuth-3.0.2/lib/Algorithm.php";
require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Rng/IRNGProvider.php";
require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Rng/CSRNGProvider.php";
require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Time/ITimeProvider.php";
require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Time/LocalMachineTimeProvider.php";
require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Qr/IQRCodeProvider.php";
require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Qr/BaconQrCodeProvider.php";

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\BaconQrCodeProvider; // if using Bacon

$tfa = new TwoFactorAuth(new BaconQrCodeProvider());
$secret = $tfa->createSecret();

echo "Enter code: <b>$secret</b>";

if(isset($_GET["totp"]))
{
    $code = $_GET["totp"];
    $secret = $_GET["secret"];
    if($tfa->verifyCode($secret, $code))
    {
        echo "Code is valid!";
    }
    else
    {
        echo "Code is invalid!";
    }
}

?>