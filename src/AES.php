<?php

class AES
{
    private string $plaintext;

    public function __construct()
    {

    }

    /**
     * @param int[][] $state
     * @return int[][]
     */
    private static function mixColumns(array $state) {

        for ($column = 0; $column < 4; $column++) {
            $a = array(4);
            $b = array(4);
            for ($i=0; $i<4; $i++) {
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

}
?>