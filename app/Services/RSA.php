<?php

namespace App\Services;

class RSA
{
    private $publicKey;
    private $privateKey;
    private $modulus;

    public function __construct($bitLength = 16)
    {
        list($p, $q) = $this->generatePrimeNumbers($bitLength);

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

    private function generatePrimeNumbers($bitLength)
    {
        do {
            $p = $this->generateRandomPrime($bitLength);
            $q = $this->generateRandomPrime($bitLength);
        } while ($p === $q);

        return [$p, $q];
    }

    private function generateRandomPrime($bitLength)
    {
        do {
            $num = $this->generateRandomNumber($bitLength);
        } while (!$this->isPrime($num));

        return $num;
    }

    private function generateRandomNumber($bitLength)
    {
        return rand(2**($bitLength-1), 2**$bitLength - 1);
    }

    private function isPrime($num)
    {
        if ($num < 2) {
            return false;
        }
        for ($i = 2, $sqrt = sqrt($num); $i <= $sqrt; $i++) {
            if ($num % $i == 0) {
                return false;
            }
        }
        return true;
    }

    // before
    // private function findPublicKey($phi)
    // {
    //     $e = 2;
    //     while ($e < $phi) {
    //         if ($this->gcd($e, $phi) == 1) {
    //             return $e;
    //         }
    //         $e++;
    //     }
    //     return null;
    // }

    private function findPublicKey($phi)
{
    do {
        // Pilih bilangan acak antara 1 hingga 100
        $e = rand(1, 1000);
    } while ($this->gcd($e, $phi) != 1);  // Ulangi sampai e relatif prima terhadap phi

    return $e;
}


    private function extendedGcd($a, $b)
{
    if ($b == 0) {
        return [$a, 1, 0];
    }

    list($gcd, $x1, $y1) = $this->extendedGcd($b, $a % $b);
    $x = $y1;
    $y = $x1 - floor($a / $b) * $y1;

    return [$gcd, $x, $y];
}

    private function findPrivateKey($e, $phi)
{
    list($g, $x) = $this->extendedGcd($e, $phi);

    if ($g != 1) {
        throw new \Exception('No modular inverse found.');
    }

    // Menghasilkan nilai positif untuk kunci privat
    return ($x % $phi + $phi) % $phi;
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
