<?php

namespace App\Services;

class RSA
{
    private $publicKey;
    private $privateKey;
    private $modulus;

    public function __construct($p, $q)
    {
        $this->modulus = $p * $q;
        $phi = ($p - 1) * ($q - 1);

        $this->publicKey = $this->findPublicKey($phi);
        $this->privateKey = $this->findPrivateKey($this->publicKey, $phi);
    }

    private function gcd($a, $b)
    {
        while ($b != 0) {
            $temp = $b;
            $b = $a % $b;
            $a = $temp;
        }
        return $a;
    }

    private function findPublicKey($phi)
    {
        $e = 2;
        while ($e < $phi) {
            if ($this->gcd($e, $phi) == 1) {
                return $e;
            }
            $e++;
        }
        return null;
    }

    private function findPrivateKey($e, $phi)
    {
        $d = 1;
        while (($e * $d) % $phi != 1) {
            $d++;
        }
        return $d;
    }

    public function encrypt($message)
    {
        $messageArray = str_split($message);
        $encryptedArray = [];
        foreach ($messageArray as $char) {
            $encryptedArray[] = bcpowmod(ord($char), $this->publicKey, $this->modulus);
        }
        return implode(',', $encryptedArray);
    }

    public function decrypt($encryptedMessage)
    {
        $encryptedArray = explode(',', $encryptedMessage);
        $decryptedArray = [];
        foreach ($encryptedArray as $value) {
            $decryptedArray[] = chr(bcpowmod($value, $this->privateKey, $this->modulus));
        }
        return implode('', $decryptedArray);
    }

    public function getPublicKey()
    {
        return $this->publicKey;
    }

    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    public function getModulus()
    {
        return $this->modulus;
    }
}

