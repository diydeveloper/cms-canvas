<?php

class Captcha extends Public_Controller
{
    public function index()
    {
        $code = substr(sha1(mt_rand()), 17, 6);

        $this->session->set_userdata('captcha_code', $code);

        $width = '120';
        $height = '40';
        $font = APPPATH . 'modules/contact/assets/fonts/monofont.ttf';

        $font_size = $height * 0.75;
        $image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');

        /* set the colours */
        $background_color = imagecolorallocate($image, 255, 255, 255);
        $text_color = imagecolorallocate($image, 20, 40, 100);
        $noise_color = imagecolorallocate($image, 100, 120, 180);

        /* generate random dots in background */
        for ($i = 0; $i < ($width * $height) / 3; $i++)
        {
            imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height), 1, 1, $noise_color);
        }

        /* generate random lines in background */
        for ( $i = 0; $i < ($width * $height) / 150; $i++ )
        {
            imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $noise_color);
        }

        /* create textbox and add text */
        $textbox = imagettfbbox($font_size, 0, $font, $code) or die('Error in imagettfbbox function');
        $x = ($width - $textbox[4])/2;
        $y = ($height - $textbox[5])/2;
        imagettftext($image, $font_size, 0, $x, $y, $text_color, $font, $code) or die('Error in imagettftext function');

        /* output captcha image to browser */
        header('Content-Type: image/jpeg');
        imagejpeg($image);
        imagedestroy($image);
    }
}