<?php
/**
 * Lido - Manager class for LIDO Records
 *
 * Copyright (C) 2016 Leipzig University Library
 *
 * @author Frank Morgner <morgnerf@ub.uni-leipzig.de>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace LidoCli\Classes;

use Prewk\XmlStringStreamer;

class Lido
{
    /**
     * Benchtime start marker
     *
     * @var int $benchTime
     * @access private
     */
    private $benchmarkTime;

    /**
     * Lido prefix within LIDO tags
     *
     * @var string $lidoPrefix
     * @access private
     */
     private $lidoPrefix;

    /**
     * Lido prefix within LIDO tags
     *
     * @var string $lidoWrapTagOpen
     * @access private
     */
    private $lidoWrapTagOpen;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->benchmarkTime = microtime(true);
    }

    /**
     * Get benchmark difference in seconds
     *
     * @return float    Benchmark difference in seconds.
     * @access private
     */
    private function getBenchmark()
    {
        return number_format((microtime(true) - $this->benchmarkTime), 4);
    }

    /**
     * Return open tag of lidoWrap with namespaces and prefix.
     *
     * @return string $lidoWrapTagOpen
     * @access private
     */
    private function getLidoWrapTagOpen()
    {
        return $this->lidoWrapTagOpen;
    }

    /**
     * Return closed tag of lidoWrap with prefix.
     *
     * @return string $lidoWrapTagOpen
     * @access private
     */
    private function getLidoWrapTagClose()
    {
        return (isset ($this->lidoPrefix) && strlen($this->lidoPrefix) > 0)
            ? "</".$this->lidoPrefix.":lidoWrap>" : "</lidoWrap>";
    }

    /**
     * Controller process of lido-cli command line tool.
     *
     * @param string $path      Path to import source
     * @param string $export Path where to export files
     * @param string $filter Filter to normalize data
     * @param string $schema Schema to which export files
     * @param int $units Units to poll of processed Lido XML. Default 1000.
     *
     * @access public
     */
    public function process(
        $path,
        $export = null,
        $filter = null,
        $schema = null,
        $units = 1000
    )
    {

        try {
            if ($export != null) {
                if (false === $this->checkIfExportFileExists($export)) {
                    print_r("File to export at " . $export . " will be created.\n");
                } else {
                    throw new \Exception('File to export at ' . $export . ' will be ' .
                        'overwritten. Due to security reasons remove previous ' .
                        'file via console or similar.');
                }
            }

            if (false === $this->checkIfLidoFileExists($path)) {
                throw new \Exception('File to import does not exist');
            }
            if (false === $this->checkIfUnitsIsInteger($units)) {
                throw new \Exception('Parameter -u|--unit have to be integer.');
            }
            if (false === $this->setLidoWrapTagOpen($path)) {
                throw new \Exception('LIDO XML isn\'t valid. Root element ' .
                    'lidoWrap hasn\'t found.');
            }

            print_r("Start streaming file: " . $path . "\n");

            $streamer = XmlStringStreamer::createStringWalkerParser($path);

            // Define local vars
            $outputCollector = '';
            $tempIterator = 1;

            // Start streaming via node
            while ($node = $streamer->getNode()) {
                // Restore LIDO XML
                $this->setLidoWrapTags($node);

                // Process XSLT transformations
                $data = $this->transformLidoWithXslt($node);

                // Process Lido XML
                $record = LidoFactory::getLidoInstance($data, $filter, $schema);

                // Put data to screen or file
                if ($export == null) {
                    $outputCollector = $record->toSolrArray();
                } else {
                    $outputCollector .= $record->toSolrJson();
                }
                // Routine to save results partly in file.
                if ($export != null) {
                    if ($tempIterator == $units) {
                        file_put_contents(
                            $export,
                            $outputCollector,
                            FILE_APPEND
                        );
                        $tempIterator = 0;
                        $outputCollector = '';
                    }
                } else {
                    print_r($outputCollector);
                }
                $tempIterator++;
            } // End while

            // Export rest of files
            if ($export != null) {
                file_put_contents(
                    $export,
                    $outputCollector,
                    FILE_APPEND
                );
            }

            print_r("All done in " . $this->getBenchmark() . "s\n");

        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    /**
     * Check if export file exists
     *
     * @param string $export
     *
     * @return mixed        Return absolute path if it exists otherwise false.
     * @access private
     * @throws \Exception   Parameter -e|export indicates to an existing directory.
     */
    private function checkIfExportFileExists($export)
    {
        $path = realpath($export);
        if (is_dir($path)) {
            throw new \Exception('Parameter -e|export indicates to an existing '
                . 'directory. Has to be filename.');
        }
        return (is_file($export)) ? true : false;
    }

    /**
     * Check if LIDO xml exists
     *
     * @param $source
     *
     * @return bool
     * @access private
     */
    private function checkIfLidoFileExists($source)
    {
        return (file_exists($source)) ? true : false;
    }

    /**
     * Check if parameter units is an integer
     *
     * @param $units
     *
     * @return bool
     * @access private
     */
    private function checkIfUnitsIsInteger($units)
    {
        return (filter_var($units, FILTER_VALIDATE_INT) !== false) ? false : true;
    }

    /**
     * Manage XSLT processor to process single unit below LIDO xml root <lidoWrap>
     *
     * @param string $node Single unit of LIDO xml
     * @param string $filter Filter identifier for LIDO xml. Currently not
     *                          implemented.
     *
     * @return string       XSLT processed single unit.
     * @access private
     * @throws \Exception    Cannot process XSLT stylesheet.
     */
    private function transformLidoWithXslt($node, $filter = null)
    {
        $xml = simplexml_load_string($node);

        // Get absolute path for xslt sheets
        $path = (realpath(dirname(__FILE__))) . '/../../xslt/';
        $lidoXslt = $path . "lido-*.xsl";
        $filterXslt = $path . $filter . "-*.xsl";
        $xsltStyleSheetsToProcess = array_merge(
            glob($lidoXslt),
            glob($filterXslt)
        );
        if (is_array($xsltStyleSheetsToProcess) &&
            count($xsltStyleSheetsToProcess) > 0
        ) {
            try {
                $xslt = new \XSLTProcessor;
                foreach ($xsltStyleSheetsToProcess as $xsltStyleSheets) {
                    //print_r("Processing XSLT stlyesheet: " . $xsltStyleSheets);
                    $template = simplexml_load_file($xsltStyleSheets);
                    $xslt->importStyleSheet($template); // import XSLT document
                    $xml = $xslt->transformToXML($xml);
                }
            } catch (\Exception $e) {
                throw new \Exception("Cannot process XSLT stylesheet: " .
                    $xsltStyleSheets);
            }
        } else {
            return $node;
        }
        return $xml;
    }

    /**
     * Set lidoWrap open tag if it doesn't exist
     *
     * @param string $source      Path to source
     * @param int $snippetLength  Limitation of chars to get head of xml document.
     *
     * @return bool
     * @access private
    */
    private function setLidoWrapTagOpen($source, $snippetLength = 1024)
    {
        $match = [];
        $snippet = file_get_contents($source, null, null, 0, $snippetLength);
        // Catch open lido wrap tag with prefix and namespaces
        if (preg_match('/(<(.*):+lidoWrap.*>)/', $snippet, $match) != 1) {
            return false;
        }
        $this->lidoWrapTagOpen = (isset($match[1]) && strlen($match[1]) > 0)
            ? $match[1] : '';

        $this->lidoPrefix = (isset($match[2]) && strlen($match[2]) > 0)
            ? $match[2] : '';

        return ($this->lidoWrapTagOpen != '') ? true : false;
    }

    /**
     * Set  open and close lidoWrap tag around given content.
     *
     * @param string $data      XML Lido container.
     *
     * @return string $data     XML with LIDO root element lidoWrap
     * @access private
     */
    private function setLidoWrapTags(&$data)
    {
        return $data =
            $this->getLidoWrapTagOpen() . $data . $this->getLidoWrapTagClose();
    }
}
