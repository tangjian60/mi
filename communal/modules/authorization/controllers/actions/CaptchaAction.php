<?php

namespace communal\modules\authorization\controllers\actions;

use Yii;
use communal\components\BaseComponent;

/**
 * Class CaptchaAction
 * @package communal\modules\authorization\controllers\actions
 */
class CaptchaAction extends \yii\captcha\CaptchaAction
{
    public $isDisturb  = false;         //干扰线
    public $isClient   = false;         //是否是app

    public function validate($input, $caseSensitive)
    {
        $code = $this->getVerifyCode();
        $valid = $caseSensitive ? ($input === $code) : strcasecmp($input, $code) === 0;

        if($this->isClient){

            $valid && $this->getVerifyCode(true);

        }else{

            $session = Yii::$app->getSession();
            $session->open();
            $name = $this->getSessionKey() . 'count';
            $session[$name] = $session[$name] + 1;

            if ($valid || $session[$name] > $this->testLimit && $this->testLimit > 0){
                $this->getVerifyCode(true);
            }

        }

        return $valid;
    }

    public function getVerifyCode($regenerate = false)
    {
        if($this->isClient){

            $oldKey = Yii::$app->request->post('captcha_key');

            if(empty($oldKey) || $regenerate){

                $code = $this->generateVerifyCode();
                $newKey = 'captchaKey' . date('His') . mt_rand(0, 9999999);
                BaseComponent::setCache($newKey, $code, 600);
                !empty($this->cacheKey) && BaseComponent::deleteCache($oldKey);
                setcookie('captcha_key', $newKey, 0, '/');

            }else{

                $code = BaseComponent::getCache($oldKey);
                BaseComponent::deleteCache($oldKey);

            }

        }else{

            $session = Yii::$app->getSession();
            $session->open();
            $name = $this->getSessionKey();
            if ($session[$name] === null || $regenerate) {
                $session[$name] = $this->generateVerifyCode();
                $session[$name . 'count'] = 1;
            }

            $code = $session[$name];
        }

        return $code;
    }

    /**
     * Renders the CAPTCHA image based on the code using GD library.
     * @param string $code the verification code
     * @return string image contents in PNG format.
     */
    protected function renderImageByGD($code)
    {
        $image = imagecreatetruecolor($this->width, $this->height);

        $backColor = imagecolorallocate(
            $image,
            (int) ($this->backColor % 0x1000000 / 0x10000),
            (int) ($this->backColor % 0x10000 / 0x100),
            $this->backColor % 0x100
        );
        imagefilledrectangle($image, 0, 0, $this->width, $this->height, $backColor);
        imagecolordeallocate($image, $backColor);

        if ($this->transparent) {
            imagecolortransparent($image, $backColor);
        }

        $foreColor = imagecolorallocate(
            $image,
            (int) ($this->foreColor % 0x1000000 / 0x10000),
            (int) ($this->foreColor % 0x10000 / 0x100),
            $this->foreColor % 0x100
        );

        //干扰
        if($this->isDisturb){
            $q_width = rand(10, $this->width -10);
            $q_y = rand(10,$this->height-10);
            $q_border = 1200;
            $sign = 1;
            $s_i = rand(10,$this->width - $q_width);
            for($q_i=0;$q_i< $q_width;$q_i++){
                $q_i%2==1 && $sign = rand(0,1)?1:-1;
                $q_y = $q_y + $sign * 0.8;
                $q_i%10==1 && $q_border += (rand(0,1)?1:-1) * rand(0, 200);
                for($j = 0; $j < 100; $j++){
                    $start_x = $s_i + $q_i + (rand(0,1)?1:-1)* rand(0,$q_border)/1000;
                    $start_y = $q_y + (rand(0,1)?1:-1)* rand(0,$q_border)/1000;

                    imagesetpixel($image, $start_x, $start_y, $foreColor);
                }
            }
        }

        $length = strlen($code);
        $box = imagettfbbox(30, 0, $this->fontFile, $code);
        $w = $box[4] - $box[0] + $this->offset * ($length - 1);
        $h = $box[1] - $box[5];
        $scale = min(($this->width - $this->padding * 2) / $w, ($this->height - $this->padding * 2) / $h);
        $x = 10;
        $y = round($this->height * 27 / 40);
        for ($i = 0; $i < $length; ++$i) {
            $fontSize = (int) (rand(26, 32) * $scale * 0.8);
            $angle = rand(-10, 10);
            $letter = $code[$i];
            $box = imagettftext($image, $fontSize, $angle, $x, $y, $foreColor, $this->fontFile, $letter);
            $x = $box[2] + $this->offset;
        }

        imagecolordeallocate($image, $foreColor);

        ob_start();
        imagepng($image);
        imagedestroy($image);

        return ob_get_clean();
    }

}
