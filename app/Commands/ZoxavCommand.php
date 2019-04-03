<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ZoxavCommand extends Command
{
    protected static $defaultName = 'check:files';

    protected $pattern = "/eval\(gzun.+\(base64_decode\(.+\)\)\);/mi";

    protected function configure()
    {
        $this->setDescription('checks the files')
             ->setHelp('This command allows you check your files')
             ->addArgument('path', InputArgument::REQUIRED, 'The path to check.')
             ->addArgument('folders', InputArgument::REQUIRED, 'The folders to check.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '============',
            'Zoxzv Started',
            '============',
            '',
        ]);

        $this->output = $output;

        $this->checkFiles($input->getArgument('path'), explode(',', $input->getArgument('folders')));
    }

    public function checkFiles(string $path, array $folders)
    {
        foreach ($folders as $folder) {
            $path = $this->getPath($path, $folder);
            $files = $this->rglob($path);
            foreach ($files as $file) {
                $this->checkFile($file);
            }
        }
    }

    public function checkFile(string $file)
    {
        if (file_exists($file)) {
            $file_contents = file_get_contents($file);
            if (preg_match($this->pattern, $file_contents)) {
                $string = preg_replace($this->pattern, '/* zoxzv removed code here */', $file_contents);
                
                file_put_contents($file, $string);

                $this->output->writeln([
                    "\n Virus in [$file] has been removed \n",
                ]);
            }
        }
    }

    // Does not support flag GLOB_BRACE
    public function rglob($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->rglob($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }

    public function getPath(string $path, string $folder)
    {
        if ($path == "base") {
            $path = base_path() . "$folder/*";
        } elseif ($path == "out") {
            $path = out_path() . "$folder/*";
        } else {
            $path = __DIR__. $path . "$folder/*";
        }
        return $path;
    }
}
