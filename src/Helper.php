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
     * @return string
     */
    public static function stateToText(array $state): string
    {
        $text = '';
        for ($i = 0; $i <sizeof($state); $i++) {
            for ($j = 0; $j < sizeof($state[$i]); $j++) {
                $text .= chr(hexdec($state[$i][$j]));
            }
        }
        return $text;
    }
}
