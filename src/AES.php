<?php

class AES
{
    private const ROUNDS = 10;
    private const RC = [ 0x01, 0x02, 0x04, 0x08, 0x10, 0x20, 0x40, 0x80, 0x1b, 0x36 ];
    private const SBOX = [
        //0    1     2     3     4     5     6     7     8     9     a     b     c     d     e     f
        [0x63, 0x7c, 0x77, 0x7b, 0xf2, 0x6b, 0x6f, 0xc5, 0x30, 0x01, 0x67, 0x2b, 0xfe, 0xd7, 0xab, 0x76], // 0
        [0xca, 0x82, 0xc9, 0x7d, 0xfa, 0x59, 0x47, 0xf0, 0xad, 0xd4, 0xa2, 0xaf, 0x9c, 0xa4, 0x72, 0xc0], // 1
        [0xb7, 0xfd, 0x93, 0x26, 0x36, 0x3f, 0xf7, 0xcc, 0x34, 0xa5, 0xe5, 0xf1, 0x71, 0xd8, 0x31, 0x15], // 2
        [0x04, 0xc7, 0x23, 0xc3, 0x18, 0x96, 0x05, 0x9a, 0x07, 0x12, 0x80, 0xe2, 0xeb, 0x27, 0xb2, 0x75], // 3
        [0x09, 0x83, 0x2c, 0x1a, 0x1b, 0x6e, 0x5a, 0xa0, 0x52, 0x3b, 0xd6, 0xb3, 0x29, 0xe3, 0x2f, 0x84], // 4
        [0x53, 0xd1, 0x00, 0xed, 0x20, 0xfc, 0xb1, 0x5b, 0x6a, 0xcb, 0xbe, 0x39, 0x4a, 0x4c, 0x58, 0xcf], // 5
        [0xd0, 0xef, 0xaa, 0xfb, 0x43, 0x4d, 0x33, 0x85, 0x45, 0xf9, 0x02, 0x7f, 0x50, 0x3c, 0x9f, 0xa8], // 6
        [0x51, 0xa3, 0x40, 0x8f, 0x92, 0x9d, 0x38, 0xf5, 0xbc, 0xb6, 0xda, 0x21, 0x10, 0xff, 0xf3, 0xd2], // 7
        [0xcd, 0x0c, 0x13, 0xec, 0x5f, 0x97, 0x44, 0x17, 0xc4, 0xa7, 0x7e, 0x3d, 0x64, 0x5d, 0x19, 0x73], // 8
        [0x60, 0x81, 0x4f, 0xdc, 0x22, 0x2a, 0x90, 0x88, 0x46, 0xee, 0xb8, 0x14, 0xde, 0x5e, 0x0b, 0xdb], // 9
        [0xe0, 0x32, 0x3a, 0x0a, 0x49, 0x06, 0x24, 0x5c, 0xc2, 0xd3, 0xac, 0x62, 0x91, 0x95, 0xe4, 0x79], // a
        [0xe7, 0xc8, 0x37, 0x6d, 0x8d, 0xd5, 0x4e, 0xa9, 0x6c, 0x56, 0xf4, 0xea, 0x65, 0x7a, 0xae, 0x08], // b
        [0xba, 0x78, 0x25, 0x2e, 0x1c, 0xa6, 0xb4, 0xc6, 0xe8, 0xdd, 0x74, 0x1f, 0x4b, 0xbd, 0x8b, 0x8a], // c
        [0x70, 0x3e, 0xb5, 0x66, 0x48, 0x03, 0xf6, 0x0e, 0x61, 0x35, 0x57, 0xb9, 0x86, 0xc1, 0x1d, 0x9e], // d
        [0xe1, 0xf8, 0x98, 0x11, 0x69, 0xd9, 0x8e, 0x94, 0x9b, 0x1e, 0x87, 0xe9, 0xce, 0x55, 0x28, 0xdf], // e
        [0x8c, 0xa1, 0x89, 0x0d, 0xbf, 0xe6, 0x42, 0x68, 0x41, 0x99, 0x2d, 0x0f, 0xb0, 0x54, 0xbb, 0x16], // f
    ];


    /** @var int[][] */
    private array $states = [];

    /** @var int[][] */
    private array $key;

    private array $allKeys;

    public function __construct(array $key)
    {
        $this->key = $key;
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
            $block[$i % 4][$col] = ($bytes[$i] ?? 0x00);

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
                $state[$i][$j] = dechex(self::SBOX[$hex / 16][$hex % 16]);
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

        for($round = 1; $round <= self::ROUNDS; $round++)
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
            $v3[$i] = dechex(self::SBOX[$hex / 16][$hex % 16]);
        }

        $v3[0] = dechex(hexdec($v3[0]) ^ self::RC[$round-1]);
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
     * @param int[][] $input
     * @param string $title
     */
    private function printArray(array $input, string $title): void
    {
        echo "<h3>$title</h3>";
        for ($i = 0; $i <sizeof($input); $i++) {
            for ($j = 0; $j < sizeof($input[$i]); $j++) {
                echo $input[$i][$j] . "\t";
            }
            echo "\n";
        }
        echo "\n";
    }

    /**
     * @param int[][] $state
     * @return string
     */
    private function stateToText(array $state): string
    {
        $text = '';
        for ($i = 0; $i <sizeof($state); $i++) {
            for ($j = 0; $j < sizeof($state[$i]); $j++) {
                $text .= chr(hexdec($state[$i][$j]));
            }
        }
        return $text;
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

            for ($round = 1; $round <= self::ROUNDS; $round++) {
                $state = $this->subBytes($state);
                $state = $this->shiftRows($state);

                if ($round !== self::ROUNDS) {
                    $state = $this->mixColumns($state);
                }

                $key = $this->getKey($round);
                $state = $this->addRoundKey($state, $key);
            }

            $this->printArray($state, "Output for Block " . ($index + 1));
            $cipherText .= $this->stateToText($state);
        }

        return $cipherText;
    }
}