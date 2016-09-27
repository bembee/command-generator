<?php

class CommandGenerator
{
    const SEGMENT = 200;

    const DIRECTORY = 'commands';

    const FILE_NAME_TEMPLATE = 'commands_%d-%d.txt';

    private $commands;

    private $pauseCommand;

    public function __construct()
    {
        $this->pauseCommand = require __DIR__ . '/data/pause.php';
        $this->commands = require __DIR__ . '/data/commands.php';
        $directory = __DIR__ . '/' . self::DIRECTORY;

        if (!is_dir($directory)) {
            mkdir($directory);
        }
    }

    public function generate()
    {
        for ($index = 0; $index < floor(9999 / self::SEGMENT) - 4; $index++) {
            $output = $this->generateSegment($index);
            file_put_contents($this->getFileName($index), $output);
        }
    }

    private function generateSegment($index)
    {
        $output = '';
        $min = $this->min($index);
        $max = $this->max($index);

        foreach (range(0, 199) as $value) {
            $number = $min + $value;
            if ($number > $max) {
                continue;
            }
            $output .= "REM " . $number . PHP_EOL;
            $output .= $this->generateCode($number);
            $output .= PHP_EOL . PHP_EOL;

            if (($value + 1) % 5 == 0) {
                $output .= $this->pauseCommand;
            }
        }

        return $output;
    }

    private function getFileName($index)
    {
        $min = $this->min($index);
        $max = $this->max($index);

        return sprintf('%s/%s/' . self::FILE_NAME_TEMPLATE, __DIR__, self::DIRECTORY, $min, $max);
    }

    /**
     * @param $index
     *
     * @return int
     */
    private function min($index)
    {
        return 1000 + $index * self::SEGMENT;
    }

    /**
     * @param $index
     *
     * @return int
     */
    private function max($index)
    {
        return 999 + ($index + 1) * self::SEGMENT;
    }

    private function generateCode($value)
    {
        $output = '';

        foreach (str_split($value) as $char) {
            $output .= $this->commands[$char];
        }

        return $output;
    }
}

$generator = new CommandGenerator();
$generator->generate();

shell_exec('zip -r commands.zip commands');