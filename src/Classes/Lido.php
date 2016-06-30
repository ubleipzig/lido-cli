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
     * @param string $source    Path to import source
     *
     * @access public
     */
    public function process($source)
    {

        try {
            if (false === $this->checkIfLidoFileExists($source)) {
                throw new \Exception('File to import does not exist');
            }

            if (false === $this->setLidoWrapTagOpen($source)) {
                throw new \Exception('LIDO XML isn\'t valid. Root element .
                    lidoWrap hasn\'t found.');
            }

            $streamer = XmlStringStreamer::createStringWalkerParser($source);

            $i = 1;
            while ($node = $streamer->getNode()) {
                $this->setLidoWrapTags($node);

                $data = $this->transformLidoWithXslt($node);

                $record = new LidoRecord($data, '', 'lido', 'lido');
                $string = $record->toSolrArray();
                //print_r($string);
                //file_put_contents('../sources/lido-unit-'.$i.'.xml', $node);
                $i++;
            }
            echo 'Done';

        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            exit(1);
        }
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
     * Manage XSLT processor to process single unit below LIDO xml root <lidoWrap>
     *
     * @param string $node  Single unit of LIDO xml
     *
     * @return string       XSLT processed single unit.
     * @access private
     */
    private function transformLidoWithXslt($node)
    {
        $xml = simplexml_load_string($node);

        $template = simplexml_load_file('./setting/lido-transform.xsl');

        $xslt = new \XSLTProcessor;
        $xslt->importStyleSheet($template); // import XSLT document

        return $xslt->transformToXML($xml);
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
