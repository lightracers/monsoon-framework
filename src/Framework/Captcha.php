<?php

namespace Framework;

class Captcha
{
    /**
     * Generate captcha image
     * @param bool $base64
     * @return string
     */
    public static function generateCaptcha($base64 = false)
    {
        $md5Hash      = md5(rand(0, 9999));
        $securityCode = substr($md5Hash, rand(0, 15), 5);
        $spook        = ': : : : : : : : : : :';
        $_SESSION["CAPTCHA_CODE"] = $securityCode;
        $width           = 100;
        $height          = 25;
        $image           = imagecreate($width, $height);
        $backgroundColor = imagecolorallocate($image, 0, 0, 0);
        $textColor       = imagecolorallocate($image, 233, 233, 233);
        $strange1Color   = imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));
        $strange2Color   = imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));
        $shape1Color     = imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));
        $shape2Color     = imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));
        imagefill($image, 0, 0, $backgroundColor);
        imagestring($image, 5, 30, 4, $securityCode, $textColor);
        imagestring($image, 0, rand(0, ($width / 2)), rand(0, $height), $spook, $strange1Color);
        imagestring($image, 0, rand(0, ($width / 2)), rand(0, $height), $spook, $strange2Color);
        imageellipse($image, 0, 0, rand(($width / 2), ($width * 2)), rand($height, ($height * 2)), $shape1Color);
        imageellipse($image, 0, 0, rand(($width / 2), ($width * 2)), rand($height, ($height * 2)), $shape2Color);
        if ($base64) {
            $path = tempnam(sys_get_temp_dir(), 'captcha_');
            imagepng($image, $path);
            $png = base64_encode(file_get_contents($path));
            unlink($path);
            imagedestroy($image);
            return $png;
        } else {
            header("Content-Type: image/png");
            ob_clean();
            imagepng($image);
            imagedestroy($image);
            die();
        }
    }

    /**
     * Return the captcha input code
     * @param string $class
     * @param string $inputName
     * @return string
     */
    public static function printCaptcha($class = '', $inputName = 'captcha')
    {
        $img      = self::captcha(true);
        $captcha  = '<img class="' . $class . '" src="data:image/png;base64,' . $img . '" alt="Captcha" />';
        $captcha .= '<input type="text" class="' . $class . '" name="' . $inputName . '">';
        return $captcha;
    }

    /**
     * Return captcha
     * @return mixed
     */
    public static function captchaCode()
    {
        return $_SESSION["CAPTCHA_CODE"];
    }

    /**
     * Validate captcha
     * @param $inputName
     * @return bool
     */
    public static function captchaVerify($inputName = 'captcha')
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (strtolower($_POST[$inputName]) == strtolower($_SESSION["CAPTCHA_CODE"]) && !empty($_SESSION["CAPTCHA_CODE"])) {
                return true;
            }

            return false;
        }

        return true;
    }
}
