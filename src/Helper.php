<?php declare(strict_types=1);

class Helper
{
    /**
     * @param int[][] $input
     * @param string|null $title
     */
    public static function printArray(array $input, ?string $title = null): void
    {
        if ($title) {
            echo "<h3>$title</h3>";
        }

        for ($i = 0; $i <sizeof($input); $i++) {
            for ($j = 0; $j < sizeof($input[$i]); $j++) {
                echo $input[$i][$j] . "\t";
            }
            echo "\n";
        }
        echo "\n";
    }


    /**
     * @param string[][] $state
     * @param bool $toBase64
     * @return string
     */
    public static function stateToText(array $state, bool $toBase64 = true): string
    {
        $text = '';
        for ($i = 0; $i < sizeof($state); $i++) {
            for ($j = 0; $j < sizeof($state[$i]); $j++) {
                $text .= $state[$j][$i];
            }
        }

        return $toBase64 ? base64_encode(pack('H*', $text)) : $text;
    }
}
