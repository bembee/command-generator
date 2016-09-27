<?php

class CommandGenerator
{
    const MIN = 1000;
    const MAX = 9999;
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
        for ($index = 0; $index <= floor((self::MAX - self::MIN) / self::SEGMENT); $index++) {
            $output = $this->generateSegment($index);
            file_put_contents($this->getFileName($index), $output);
        }
    }

    private function generateSegment($index)
    {
        $output = '';
        $min = $this->min($index);
        $max = $this->max($index);

        for ($value = $min; $value <= $max; $value++) {
            $output .= "REM " . $value . PHP_EOL;
            $output .= $this->generateCode($value);
            $output .= PHP_EOL . PHP_EOL;

            if (($value + 1) % 5 == 0 && $value < 199) {
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
        return min(999 + ($index + 1) * self::SEGMENT, self::MAX);
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