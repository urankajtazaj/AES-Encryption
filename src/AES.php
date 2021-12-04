<?php

class AES
{
    private string $plaintext;

    public function __construct()
    {

    }

    /**
     * @param int[][] $state
     * @param $number
     * @return int[][]
     */
    private static function mixColumns(array $state, $number) {

        for ($column = 0; $column < $number; $column++) {
            $a = $number;
            $b = $number;
            for ($i=0; $i<$number; $i++) {
                $a[$i] = $state[$i][$column];
                $b[$i] = $state[$i][$column]&0x80 ? $state[$i][$column]<<1 ^ 0x011b : $state[$i][$column]<<1;
                /* GF modulo: if $state[$i][$column] >= 128, then it will overflow when shifted left, so reduce */
                //XOR with the primitive polynomial x^8 + x^4 + x^3 + x + 1 (0b1_0001_1011) – you can change it but it must be irreducible */
            }

            $state[0][$column] = $b[0] ^ $a[1] ^ $b[1] ^ $a[2] ^ $a[3];
            $state[1][$column] = $a[0] ^ $b[1] ^ $a[2] ^ $b[2] ^ $a[3];
            $state[2][$column] = $a[0] ^ $a[1] ^ $b[2] ^ $a[3] ^ $b[3];
            $state[3][$column] = $a[0] ^ $b[0] ^ $a[1] ^ $a[2] ^ $b[3];
        }
        return $state;
    }

    //XOR me key e gjenerum per mem na jep rez, qe osht state
    //pozita (0,0) e state qe vjen prej function para addRoundKey,ka me u XOR me Key ne poziten(0,0)...(3,3)
    private static function addRoundKey($state, $expandedKey, $round, $number)
    {
        for ($rows = 0; $rows < $number; $rows++) {
            for ($column = 0; $column < $number; $column++) {
                $state[$rows][$column] ^= $expandedKey[$round * $number + $column][$rows];
            }
        }
        return $state;
    }

}
?>