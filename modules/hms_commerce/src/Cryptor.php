<?php

namespace Drupal\hms_commerce;

/**
 * Cryptor drupal service class.
 */
class Cryptor {

  private function hex_chars($data) {
    $hex = '';
    for ($i = 0; $i < strlen($data); $i++) {
      $c = substr($data, $i, 1);
      //$hex .= '{'. hex_format(ord($c)). '}';
      $hex .= $this->hex_format(ord($c));
    }
    return $hex;
  }

  private function hex_format($o) {
    $h = (dechex($o));
    $len = strlen($h);
    if ($len % 2 == 1) {
      $h = "0$h";
    }
    return $h;
  }


  private function uniord($c) {
    if (ord($c{0}) >= 0 && ord($c{0}) <= 127) {
      return ord($c{0});
    }
    if (ord($c{0}) >= 192 && ord($c{0}) <= 223) {
      return (ord($c{0}) - 192) * 64 + (ord($c{1}) - 128);
    }
    if (ord($c{0}) >= 224 && ord($c{0}) <= 239) {
      return (ord($c{0}) - 224) * 4096 + (ord($c{1}) - 128) * 64 + (ord($c{2}) - 128);
    }
    if (ord($c{0}) >= 240 && ord($c{0}) <= 247) {
      return (ord($c{0}) - 240) * 262144 + (ord($c{1}) - 128) * 4096 + (ord($c{2}) - 128) * 64 + (ord($c{3}) - 128);
    }
    if (ord($c{0}) >= 248 && ord($c{0}) <= 251) {
      return (ord($c{0}) - 248) * 16777216 + (ord($c{1}) - 128) * 262144 + (ord($c{2}) - 128) * 4096 + (ord($c{3}) - 128) * 64 + (ord($c{4}) - 128);
    }
    if (ord($c{0}) >= 252 && ord($c{0}) <= 253) {
      return (ord($c{0}) - 252) * 1073741824 + (ord($c{1}) - 128) * 16777216 + (ord($c{2}) - 128) * 262144 + (ord($c{3}) - 128) * 4096 + (ord($c{4}) - 128) * 64 + (ord($c{5}) - 128);
    }
    if (ord($c{0}) >= 254 && ord($c{0}) <= 255)    //  error
    {
      return FALSE;
    }
    return 0;
  }

  private function unichr($o) {
    return mb_convert_encoding('&#' . intval($o) . ';', 'UTF-8', 'HTML-ENTITIES');
  }

  private function xor_string($string, $key) {
    $result = "";

    $chrArray = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
    for ($i = 0; $i < count($chrArray); $i++) {
      $ord = $this->uniord($chrArray[$i]);

      $keyCharOrd = $this->uniord($key[$i % mb_strlen($key)]);

      $result .= $this->unichr($ord ^ $keyCharOrd);
    }
    return $result;
  }

  public function getCryptoKeyForContenId($contentId) {
    $SHARD_SECRET_KEY = "This is a shard secret between drupal and the usermanger"; //todo
    return hash_hmac('sha1', $contentId, $SHARD_SECRET_KEY);
  }

  public function encodeContent($contentId, $string) {
    $key = $this->getCryptoKeyForContenId($contentId);
    $content = $this->hex_chars($this->xor_string($string, $key));
    return $content;
  }
}
