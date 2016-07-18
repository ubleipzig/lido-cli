<?php
/**
 * Lido - Schema Finc Mapper class for LIDO Records
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

class LidoSchemaFinc implements LidoSchemaInterface
{
    /**
     * Lido Record object
     *
     * @var object $lidoRecord Lido Record object
     * @access private
     *
     */
    private $lidoRecord;

    /**
     * LidoSchema constructor
     *
     * @param $lidoRecord Lido record object
     *
     * @access public
     */
    public function __construct($lidoRecord)
    {
        $this->lidoRecord = $lidoRecord;
    }

    /**
     * Collector for array to import in Solr
     *
     * @return array    Returns array to import in Solr
     * @access public
     */
    public function toSolrArray()
    {
        $record = $this->lidoRecord->toSolrArray();
        $dateRange = $this->lidoRecord->getDateRange();
        if ($dateRange) {
            $record['publishDateSort']
                = $this->lidoRecord->getPublishDateSort($dateRange[0]);
            $record['dateSpan']
                = $record['publishDate']
                = $this->lidoRecord->getDateRangeForStringType($dateRange);
        }
        // Author
        $record['author'] = $this->lidoRecord->getAuthors();
        $record['author_role'] = $this->lidoRecord->getAuthorRoles();
        $record['author_id'] = $this->lidoRecord->getAuthorIds();
        // Collection
        $record['collection']
            = $this->lidoRecord->getRelatedWorkDisplayObject(['relatedWork']);
        // Institution
        $record['institution'] = $this->lidoRecord->getInstitution();
        // Source identifier
        $record['source_id'] = $this->lidoRecord->getSourceId();
        // Recordtype
        $record['recordtype'] = $this->lidoRecord->getRecordType();
        // Urls
        $record['url'][] = $this->lidoRecord->getRecordInfoLink();
        return $record;
    }

    /**
     * Collector for json to import in Solr
     *
     * @return string    Returns json to import in Solr
     * @access public
     */
    public function toSolrJson()
    {
        return json_encode($this->toSolrArray(), JSON_UNESCAPED_UNICODE) . "\n";
    }
}