<?php

use AES as GlobalAES;

class AES
{
    private const SBOX = [
        [0x63, 0x7c, 0x77, 0x7b, 0xf2, 0x6b, 0x6f, 0xc5, 0x30, 0x01, 0x67, 0x2b, 0xfe, 0xd7, 0xab, 0x76],
        [0xca, 0x82, 0xc9, 0x7d, 0xfa, 0x59, 0x47, 0xf0, 0xad, 0xd4, 0xa2, 0xaf, 0x9c, 0xa4, 0x72, 0xc0],
        [0xb7, 0xfd, 0x93, 0x26, 0x36, 0x3f, 0xf7, 0xcc, 0x34, 0xa5, 0xe5, 0xf1, 0x71, 0xd8, 0x31, 0x15],
        [0x04, 0xc7, 0x23, 0xc3, 0x18, 0x96, 0x05, 0x9a, 0x07, 0x12, 0x80, 0xe2, 0xeb, 0x27, 0xb2, 0x75],
        [0x09, 0x83, 0x2c, 0x1a, 0x1b, 0x6e, 0x5a, 0xa0, 0x52, 0x3b, 0xd6, 0xb3, 0x29, 0xe3, 0x2f, 0x84],
        [0x53, 0xd1, 0x00, 0xed, 0x20, 0xfc, 0xb1, 0x5b, 0x6a, 0xcb, 0xbe, 0x39, 0x4a, 0x4c, 0x58, 0xcf],
        [0xd0, 0xef, 0xaa, 0xfb, 0x43, 0x4d, 0x33, 0x85, 0x45, 0xf9, 0x02, 0x7f, 0x50, 0x3c, 0x9f, 0xa8],
        [0x51, 0xa3, 0x40, 0x8f, 0x92, 0x9d, 0x38, 0xf5, 0xbc, 0xb6, 0xda, 0x21, 0x10, 0xff, 0xf3, 0xd2],
        [0xcd, 0x0c, 0x13, 0xec, 0x5f, 0x97, 0x44, 0x17, 0xc4, 0xa7, 0x7e, 0x3d, 0x64, 0x5d, 0x19, 0x73],
        [0x60, 0x81, 0x4f, 0xdc, 0x22, 0x2a, 0x90, 0x88, 0x46, 0xee, 0xb8, 0x14, 0xde, 0x5e, 0x0b, 0xdb],
        [0xe0, 0x32, 0x3a, 0x0a, 0x49, 0x06, 0x24, 0x5c, 0xc2, 0xd3, 0xac, 0x62, 0x91, 0x95, 0xe4, 0x79],
        [0xe7, 0xc8, 0x37, 0x6d, 0x8d, 0xd5, 0x4e, 0xa9, 0x6c, 0x56, 0xf4, 0xea, 0x65, 0x7a, 0xae, 0x08],
        [0xba, 0x78, 0x25, 0x2e, 0x1c, 0xa6, 0xb4, 0xc6, 0xe8, 0xdd, 0x74, 0x1f, 0x4b, 0xbd, 0x8b, 0x8a],
        [0x70, 0x3e, 0xb5, 0x66, 0x48, 0x03, 0xf6, 0x0e, 0x61, 0x35, 0x57, 0xb9, 0x86, 0xc1, 0x1d, 0x9e],
        [0xe1, 0xf8, 0x98, 0x11, 0x69, 0xd9, 0x8e, 0x94, 0x9b, 0x1e, 0x87, 0xe9, 0xce, 0x55, 0x28, 0xdf],
        [0x8c, 0xa1, 0x89, 0x0d, 0xbf, 0xe6, 0x42, 0x68, 0x41, 0x99, 0x2d, 0x0f, 0xb0, 0x54, 0xbb, 0x16]
    ];

    private const RC=[0x01,0x02,0x04,0x08,0x10,0x20,0x40,0x80,0x1b,0x36];
    private array $allKeys;
    /**
     * @param string $plaintext
     * @param string $initialKey
     */
    public function __construct()
    {
    }

    /**
    *
    * @param string $string
    * @return string $string 
    */
    private function makeTwoChars($string)
    {
        if(strlen($string)==1)
        {
            $string="0".$string;
        }
        return $string;
    }


    /**
    *
    * @param string[][] $key
    * @return int[][] $roundKey 
    */
    private function calculateRoundKey($key,$round): array
    {
        $v0=array();
        $v1=array();
        $v2=array();
        $v3=array();
        $index=0;
        for($i=0 ; $i<count($key); $i++)
        {
            for($j=0 ; $j<count($key); $j++)
            {
                switch ($i) {
                    case 0:
                        $v0[$j]=$key[$j][$i];
                        break;
                    case 1:
                        $v1[$j]=$key[$j][$i];
                        break;
                    case 2:
                        $v2[$j]=$key[$j][$i];
                        break;
                    case 3:
                        $v3[$j]=$key[$j][$i];
                        break;
                }

            }

        } 

        $w3_org=$v3;
        //Shift for one to left
        $v3_0copy=$v3[0];
        $v3[0]=$v3[1];
        $v3[1]=$v3[2];
        $v3[2]=$v3[3];
        $v3[3]=$v3_0copy;
    
        //Use sbox -fill with 0 before if string is only one char
        for($i=0 ; $i<count($v0) ; $i++)
        {
            $v3[$i]=$this->makeTwoChars(dechex(self::SBOX[hexdec(substr($v3[$i], 0,1))][hexdec(substr($v3[$i], 1,2))]));
        }
        //XOR round coefficient widh first elem
        $v3[0]=$this->makeTwoChars(dechex(hexdec($v3[0]) ^ self::RC[$round-1]));
        for($i=0 ; $i<count($v0); $i++)
        {
            $v0[$i]=$this->makeTwoChars(dechex(hexdec($v0[$i]) ^ hexdec($v3[$i])));
            $v1[$i]=$this->makeTwoChars(dechex(hexdec($v0[$i]) ^ hexdec($v1[$i])));
            $v2[$i]=$this->makeTwoChars(dechex(hexdec($v1[$i]) ^ hexdec($v2[$i])));
            $v3[$i]=$this->makeTwoChars(dechex(hexdec($v2[$i]) ^ hexdec($w3_org[$i])));
        }

        //merging arrays into 2d array
        $result=$key;
        for($elem=0; $elem<count($v0); $elem++)
        {
            
            $result[$elem][0] = strtoupper($v0[$elem]);
            $result[$elem][1] = strtoupper($v1[$elem]);
            $result[$elem][2] = strtoupper($v2[$elem]);
            $result[$elem][3] = strtoupper($v3[$elem]);
        }

        return $result;

    }


    /**
     *
     * @param int[][] $initialKey
     * @return int[][] $w
     */
    public function generateKeys(array $initialKey)
    {
        $w =array();//holds all 10 keys
        $index=0;
        //Fill first key in the array
        for($i=0 ; $i<count($initialKey); $i++)
        {
            $w[$i]=$initialKey[$i];
            $index++;
        }
        $roundKey=$w;//this is 4x4 matrix with a key for every round

        //Calculate 10 keys and assign to variable "w"
        for($round=1 ; $round<=10 ; $round++)
        {
            $roundKey=$this->calculateRoundKey($roundKey,$round);
            $w = array_merge($w, $roundKey); //merging keys together (adding -this- round key to overall keys)
        }  

        // for($i=0 ; $i<count($w); $i++)
        // {
        //     echo implode("",$w[$i]);
        //     echo("\n");
        // }
        $this->allKeys = $w;
    }

    /**
     *
     * @param int $round
     * @return int[][] $thisRoundKey
     */
    public function getKey($round):array
    {
        $thisRoundKey=array();
        $start=$round*4;
        $end=$start+4;
        $count=0;
        while($start<$end)
        {
            $thisRoundKey[$count]=$this->allKeys[$start];
            $start++;
            $count++;
        }
        return $thisRoundKey;

    }
}
// $testObject = new GlobalAES();

// $key = array(
//     array( "2B", "28","AB","09"),
//     array( "7E", "AE","F7","CF"),
//     array( "15", "D2","15","4F"),
//     array( "16", "A6","88","3C"));
// $testObject->generateKeys($key);

// print_r($testObject->getKey(10));