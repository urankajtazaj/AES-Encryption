<?php

class AES
{
    /** @var int[][] */
    private array $states = [];

    /** @var int[][] */
    private array $key;

    private array $allKeys;

    public function __construct(string $plainKey)
    {
        $this->key = $this->createKeyBlock($plainKey);
    }

    /**
     * @param array $key
     * @return array
     */
    private function createKeyBlock(string $key)
    {
        $block = [];
        $col = 0;
        for ($i = 0; $i < strlen($key); $i++) {
            $block[$i % 4][$col] = dechex(ord($key[$i]));

            if (($i + 1) % 4 === 0) {
                $col++;
            }
        }

        return $block;
    }

    private function generateBlocks(string $plaintext): array
    {
        $tempState = [];
        $row = 0;

        for ($i = 0; $i < strlen($plaintext); $i++) {
            $tempState[$row][$i % 16] = dechex(ord($plaintext[$i]));

            if (($i + 1) % 16 === 0) {
                $row++;
            }
        }

        $blocks = [];
        foreach ($tempState as $block) {
            $blocks[] = $this->createBlock($block);
        }

        return $blocks;
    }

    private function createBlock(array $bytes): array
    {
        $bytesLength = sizeof($bytes);
        $block = [];
        $col = 0;

        for ($i = 0; $i < $bytesLength + (16 - $bytesLength); $i++) {
            $block[$i % 4][$col] = ($bytes[$i] ?? 0x0);

            if (($i + 1) % 4 === 0) {
                $col++;
            }
        }

        return $block;
    }

    /**
     * @param int[][] $state
     * @return int[][]
     */
    private function subBytes(array $state): array
    {
        for ($i = 0; $i < sizeof($state); $i++) {
            for ($j = 0; $j < sizeof($state[0]); $j++) {
                $hex = hexdec($state[$i][$j]);
                $state[$i][$j] = dechex(Constant::SBOX[$hex / 16][$hex % 16]);
            }
        }

        return $state;
    }

    /**
     * @param int[][] $state
     * @return int[][]
     */
    private function shiftRows(array $state): array
    {
        $tempState = $state;
        for ($i = 1; $i < sizeof($state); $i++) {
            for ($j = sizeof($state[$i]) - 1; $j >= 0; $j--) {
                $state[$i][$j] = $tempState[$i][($j + $i) % 4];
            }
        }

        return $state;
    }

    private function generateKeys(): void
    {
        $keys = [];
        $index = 0;

        for($i = 0; $i < count($this->key); $i++)
        {
            $keys[$i] = $this->key[$i];
            $index++;
        }

        $roundKey = $keys;

        for($round = 1; $round <= Constant::ROUNDS; $round++)
        {
            $roundKey=$this->calculateRoundKey($roundKey, $round);
            $keys = array_merge($keys, $roundKey);
        }
        $this->allKeys = $keys;
    }

    /**
     * @param string[][] $key
     * @param int $round
     * @return int[][]
     */
    private function calculateRoundKey(array $key, int $round): array
    {
        $v0 = [];
        $v1 = [];
        $v2 = [];
        $v3 = [];

        for($i = 0; $i < count($key); $i++) {
            for($j = 0; $j < count($key); $j++) {
                switch ($i) {
                    case 0:
                        $v0[$j] = $key[$j][$i];
                        break;
                    case 1:
                        $v1[$j] = $key[$j][$i];
                        break;
                    case 2:
                        $v2[$j] = $key[$j][$i];
                        break;
                    case 3:
                        $v3[$j] = $key[$j][$i];
                        break;
                }
            }
        } 

        $w3_org=$v3;
        $v3_0copy=$v3[0];

        $v3[0]=$v3[1];
        $v3[1]=$v3[2];
        $v3[2]=$v3[3];
        $v3[3]=$v3_0copy;
    
        for($i = 0; $i < count($v0); $i++) {
            $hex = hexdec($v3[$i]);
            $v3[$i] = dechex(Constant::SBOX[$hex / 16][$hex % 16]);
        }

        $v3[0] = dechex(hexdec($v3[0]) ^ Constant::RC[$round-1]);
        for($i = 0; $i < count($v0); $i++) {
            $v0[$i] = dechex(hexdec($v0[$i]) ^ hexdec($v3[$i]));
            $v1[$i] = dechex(hexdec($v0[$i]) ^ hexdec($v1[$i]));
            $v2[$i] = dechex(hexdec($v1[$i]) ^ hexdec($v2[$i]));
            $v3[$i] = dechex(hexdec($v2[$i]) ^ hexdec($w3_org[$i]));
        }

        $result = $key;
        for($elem = 0; $elem < count($v0); $elem++) {
            $result[$elem][0] = strtoupper($v0[$elem]);
            $result[$elem][1] = strtoupper($v1[$elem]);
            $result[$elem][2] = strtoupper($v2[$elem]);
            $result[$elem][3] = strtoupper($v3[$elem]);
        }

        return $result;
    }

    /**
     * @param int $round
     * @return int[][] $thisRoundKey
     */
    private function getKey(int $round):array
    {
        $thisRoundKey = array();
        $start = $round * 4;
        $end = $start + 4;
        $count = 0;

        while($start < $end)
        {
            $thisRoundKey[$count] = $this->allKeys[$start];
            $start++;
            $count++;
        }

        return $thisRoundKey;
    }

    /**
     * @param int[][] $state
     * @return int[][]
     */
    private static function mixColumns(array $state): array {

        for ($column = 0; $column < 4; $column++) {
            $a = [];
            $b = [];
            for ($i = 0; $i < 4; $i++) {
                $a[] = hexdec($state[$i][$column]);
                $b[] = hexdec($state[$i][$column]) & 0x80 ? hexdec($state[$i][$column]) << 1 ^ 0x011b : hexdec($state[$i][$column]) << 1;
            }

            $state[0][$column] = dechex($b[0] ^ $a[1] ^ $b[1] ^ $a[2] ^ $a[3]);
            $state[1][$column] = dechex($a[0] ^ $b[1] ^ $a[2] ^ $b[2] ^ $a[3]);
            $state[2][$column] = dechex($a[0] ^ $a[1] ^ $b[2] ^ $a[3] ^ $b[3]);
            $state[3][$column] = dechex($a[0] ^ $b[0] ^ $a[1] ^ $a[2] ^ $b[3]);
        }

        return $state;
    }

    /**
     * @param int[][] $state
     * @param int[][] $expandedKey
     * @return int[][]
     */
    private static function addRoundKey(array $state, array $expandedKey): array
    {
        for ($rows = 0; $rows < 4; $rows++) {
            for ($column = 0; $column < 4; $column++) {
                $state[$rows][$column] = dechex(hexdec($state[$rows][$column]) ^ hexdec($expandedKey[$rows][$column]));
            }
        }

        return $state;
    }

    /**
     * @param string $plaintext
     * @return string
     */
    public function encrypt(string $plaintext): string
    {
        $this->states = $this->generateBlocks($plaintext);
        $this->generateKeys();

        $cipherText = '';
        foreach ($this->states as $index => $state) {
            $key = $this->getKey(0);
            $state = $this->addRoundKey($state, $key);

            for ($round = 1; $round <= Constant::ROUNDS; $round++) {
                $state = $this->subBytes($state);
                $state = $this->shiftRows($state);

                if ($round !== Constant::ROUNDS) {
                    $state = $this->mixColumns($state);
                }

                $key = $this->getKey($round);
                $state = $this->addRoundKey($state, $key);
            }

            Helper::printArray($state, "Output for Block " . ($index + 1));
            $cipherText .= Helper::stateToText($state, false);
        }

        return base64_encode(pack('H*', $cipherText));
    }
}