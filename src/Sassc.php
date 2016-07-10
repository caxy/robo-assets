<?php

namespace Bangpound\Robo\Task\Assets;

use Robo\Common\ExecOneCommand;
use Robo\Contract\CommandInterface;
use Robo\Exception\TaskException;
use Robo\Result;
use Robo\Task\Assets\CssPreprocessor;
use Symfony\Component\Process\Process;

class Sassc extends CssPreprocessor implements CommandInterface
{
    use ExecOneCommand;

    const FORMAT_NAME = 'scss';

    protected $command;

    protected $compilers = [
      'sassc', // https://github.com/sass/sassc
    ];

    protected function sassc($file)
    {
        $this->printTaskInfo('Sassc: {file} {arguments}', ['file' => $file, 'arguments' => $this->arguments]);

        // For stdin compiles:
        // $this->option('stdin');
        // $this->printed(false);
        // $scssCode = file_get_contents($file);
        // $result = $this->executeCommandStdin($this->getCommand(), $scssCode);



        if (isset($this->compilerOptions['importDirs'])) {
            $this->optionList('load-path', $this->compilerOptions['importDirs']);
        }

        if (isset($this->compilerOptions['formatter'])) {
            $this->option('style', $this->compilerOptions['formatter']);
        }

        $output = $this->files[$file];
        $this->arg($file);
        $this->arg($output);
        $result = $this->executeCommand($this->getCommand());

        return file_get_contents($output);
    }

    protected function executeCommandStdin($command, $input = null)
    {
        $process = new Process($command);
        $process->setTimeout(null);
        if ($input) {
            $process->setInput($input);
        }
        if ($this->workingDirectory) {
            $process->setWorkingDirectory($this->workingDirectory);
        }
        $this->startTimer();
        if ($this->isPrinted) {
            $process->run(function ($type, $buffer) { echo $buffer; });
        } else {
            $process->run();
        }
        $this->stopTimer();

        return new Result($this, $process->getExitCode(), $process->getOutput(), ['time' => $this->getExecutionTime()]);
    }

    public function __construct(array $input, $pathToSassc = null)
    {
        $this->command = $pathToSassc;
        if (!$this->command) {
            $this->command = $this->findExecutable('sassc');
        }
        if (!$this->command) {
            throw new TaskException(__CLASS__, 'Sassc executable not found.');
        }
        parent::__construct($input);
    }

    public function getCommand()
    {
        return "{$this->command} {$this->arguments}";
    }
}

/*

Usage: sassc [options] [INPUT] [OUTPUT]

Options:
  -s, --stdin             Read input from standard input instead of an input file.
  -t, --style NAME        Output style. Can be: nested, expanded, compact, compressed.
  -l, --line-numbers      Emit comments showing original line numbers.
  --line-comments
  -I, --load-path PATH    Set Sass import path.
  -P, --plugin-path PATH  Set path to autoload plugins.
  -m, --sourcemap         Emit source map.
  -M, --omit-map-comment  Omits the source map url comment.
  -p, --precision         Set the precision for numbers.
  -v, --version           Display compiled versions.
  -h, --help              Display this help message.

 */
