<?php
/**
 * BaseRecord Class
 *
 * PHP version 5
 *
 * Copyright (C) The National Library of Finland 2011-2014.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category DataManagement
 * @package  RecordManager
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/KDK-Alli/RecordManager
 */

/**
 * BaseRecord Class
 *
 * This is a base class for processing records.
 *
 * @category DataManagement
 * @package  RecordManager
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/KDK-Alli/RecordManager
 */
namespace LidoCli\Classes;

class BaseRecord
{
    // Record source ID
    protected $source;

    // Record ID prefix
    protected $idPrefix = '';

    /**
     * Constructor
     *
     * @param string $data     Metadata
     * @param string $oaiID    Record ID received from OAI-PMH (or empty string for
     * file import)
     * @param string $source   Source ID
     * @param string $idPrefix Record ID prefix
     */
    public function __construct($data, $oaiID, $source, $idPrefix)
    {
        $this->source = $source;
        $this->idPrefix = $idPrefix;
    }

    /**
     * Return record ID (unique in the data source)
     *
     * @return string
     */
    public function getID()
    {
        die('unimplemented');
    }

    /**
     * Return record linking ID (typically same as ID) used for links
     * between records in the data source
     *
     * @return string
     */
    public function getLinkingID()
    {
        return $this->getID();
    }

    /**
     * Serialize the record for storing in the database
     *
     * @return string
     */
    public function serialize()
    {
        die('unimplemented');
    }

    /**
     * Serialize the record into XML for export
     *
     * @return string
     */
    public function toXML()
    {
        die('unimplemented');
    }

    /**
     * Normalize the record (optional)
     *
     * @return void
     */
    public function normalize()
    {
    }

    /**
     * Return whether the record is a component part
     *
     * @return boolean
     */
    public function getIsComponentPart()
    {
        return false;
    }

    /**
     * Return host record ID for component part
     *
     * @return string
     */
    public function getHostRecordID()
    {
        return '';
    }

    /**
     * Return fields to be indexed in Solr (an alternative to an XSL transformation)
     *
     * @return string[]
     */
    public function toSolrArray()
    {
        return '';
    }

    /**
     * Merge component parts to this record
     *
     * @param MongoCollection $componentParts Component parts to be merged
     *
     * @return void
     */
    public function mergeComponentParts($componentParts)
    {
    }

    /**
     * Return record title
     *
     * @param bool $forFiling Whether the title is to be used in filing
     * (e.g. sorting, non-filing characters should be removed)
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getTitle($forFiling = false)
    {
        return '';
    }

    /**
     * Component parts: get the volume that contains this component part
     *
     * @return string
     */
    public function getVolume()
    {
        return '';
    }

    /**
     * Component parts: get the issue that contains this component part
     *
     * @return string
     */
    public function getIssue()
    {
        return '';
    }

    /**
     * Component parts: get the start page of this component part in the host record
     *
     * @return string
     */
    public function getStartPage()
    {
        return '';
    }

    /**
     * Component parts: get the container title
     *
     * @return string
     */
    public function getContainerTitle()
    {
        return '';
    }

    /**
     * Component parts: get the reference to the part in the container
     *
     * @return string
     */
    public function getContainerReference()
    {
        return '';
    }

    /**
     * Dedup: Return full title (for debugging purposes only)
     *
     * @return string
     */
    public function getFullTitle()
    {
        return '';
    }

    /**
     * Dedup: Return main author (format: Last, First)
     *
     * @return string
     */
    public function getMainAuthor()
    {
        return '';
    }

    /**
     * Dedup: Return unique IDs (control numbers)
     *
     * @return string[]
     */
    public function getUniqueIDs()
    {
        return [];
    }

    /**
     * Dedup: Return (unique) ISBNs in ISBN-13 format without dashes
     *
     * @return string[]
     */
    public function getISBNs()
    {
        return [];
    }

    /**
    * Dedup: Return ISSNs
    *
    * @return string[]
    */
    public function getISSNs()
    {
        return [];
    }

    /**
     * Dedup: Return series ISSN
     *
     * @return string
     */
    public function getSeriesISSN()
    {
        return '';
    }

    /**
     * Dedup: Return series numbering
     *
     * @return string
     */
    public function getSeriesNumbering()
    {
        return '';
    }

    /**
     * Dedup: Return format from predefined values
     *
     * @return string
     */
    public function getFormat()
    {
        return '';
    }

    /**
     * Dedup: Return publication year (four digits only)
     *
     * @return string
     */
    public function getPublicationYear()
    {
        return '';
    }

    /**
     * Dedup: Return page count (number only)
     *
     * @return string
     */
    public function getPageCount()
    {
        return '';
    }

    /**
     * Dedup: Add the dedup key to a suitable field in the metadata.
     * Used when exporting records to a file.
     *
     * @param string $dedupKey Dedup key to be added
     *
     * @return void
     */
    public function addDedupKeyToMetadata($dedupKey)
    {
    }

    /**
     * Check if record has access restrictions.
     *
     * @return string 'restricted' or more specific licence id if restricted,
     * empty string otherwise
     */
    public function getAccessRestrictions()
    {
        return '';
    }

    /**
     * Return a parameter specified in driverParams[] of datasources.ini
     *
     * @param string $parameter Parameter name
     * @param bool   $default   Default value if the parameter is not set
     *
     * @return mixed Value
     */
    protected function getDriverParam($parameter, $default = true)
    {
        global $configArray;

        if (!isset($configArray['dataSourceSettings'][$this->source]['driverParams'])
        ) {
            return $default;
        }
        $iniValues = parse_ini_string(
            implode(
                PHP_EOL,
                $configArray['dataSourceSettings'][$this->source]['driverParams']
            )
        );

        return isset($iniValues[$parameter]) ? $iniValues[$parameter] : $default;
    }

    /**
     * Verify that a string is valid ISO8601 date
     *
     * @param string $dateString Date string
     *
     * @return string Valid date string or an empty string if invalid
     */
    protected function validateDate($dateString)
    {
        if (MetadataUtils::validateISO8601Date($dateString) !== false) {
            return $dateString;
        }
        return '';
    }
}

