<?php
/**
 * Command line tool for LIDO XML sources.
 *
 * Copyright (C) 2016 Leipzig University Library
 *
 * @author Frank Morgner <morgnerf@ub.uni-leipzig.de>
 *
 *
 * This program comes with ABSOLUTELY NO WARRANTY; for details type
 * `lido-cli.php --help'.
 * This is free software, and you are welcome to redistribute it
 * under certain conditions; type `lido-cli.php --license' for details.
 *
 * @see http://ulrichsg.github.io/getopt-php
 */
namespace LidoCli;

require_once __DIR__.'/../vendor/autoload.php';

use Ulrichsg\Getopt\Getopt as Getopt;
use LidoCli\Classes\Lido;

class LidoClient
{
    public function main()
    {

        $getopt = new Getopt([
            ['h','help', GetOpt::NO_ARGUMENT, 'Help manual'],
            [null,'license', GetOpt::NO_ARGUMENT, 'License GNU GPL V3 text'],
            ['s','source', GetOpt::REQUIRED_ARGUMENT, 'Path to source file']
        ]);

        try {
            $getopt->parse();

            // Manual options
            if ($getopt->getOption('help')) {
                echo $getopt->getHelpText();
                exit(0);
            }
            if ($getopt->getOption('license')) {
                echo $this->getLicenseText();
                exit(0);
            }

            // Process options
            if ($source = $getopt->getOption('s')) {
                // start processing here
                $obj = new Lido();
                $obj->process($source);
            }


        } catch (\UnexpectedValueException $e) {
            echo "Error: " . $e->getMessage() . "\n";
            echo $getopt->getHelpText();
            exit(1);
        }
    }

    /**
     * Get license text
     *
     * @return string   Return licence text
     *
     */
    private function getLicenseText()
    {
        if (false === file_exists(__DIR__.'/../LICENSE')) {
            return 'No license text available';
        }
        return file_get_contents(__DIR__.'/../LICENSE');
    }
}

$execute = new LidoClient();
$execute->main();
