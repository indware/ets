<?php
/**
 * Created by PhpStorm.
 * User: Souvik Hazra
 * Date: 28-02-2016
 * Time: 01:44 PM
 */

namespace app\Util;


class CryptoUtil
{
  public static function getRandomHash($id)
  {
    return md5(uniqid($id, true));
  }

  public static function getSecureRandomHash($length = 16)
  {
    return bin2hex(openssl_random_pseudo_bytes($length));
  }
}