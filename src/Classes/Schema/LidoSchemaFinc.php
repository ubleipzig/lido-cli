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
namespace LidoCli\Classes\Schema;

use \LidoCli\Classes\Storage as Storage;
use \LidoCli\Classes\Utils as Utils;

class LidoSchemaFinc implements LidoSchemaInterface
{
    use LidoSchemaTrait;

    /**
     * Config object
     *
     * @var object $config Config object
     * @access private
     *
     */
    private $config;

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
     * Please note: GetRecordIdentifier method depends on initialized lidoRecord.
     *
     * @param $lidoRecord Lido record object
     *
     * @access public
     */
    public function __construct($lidoRecord)
    {
        $this->lidoRecord = $lidoRecord;
        $this->config = Storage\RegistryConfig::getInstance()
            ->loadSchema(
                $this->getSchemaIdentifier(),
                $this->getRecordIdentifier()
            )
            ->getArray();
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
        $this->getRecordId($record);
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
        // Author VF1
        $record['vf1_author'] = $this->lidoRecord->getAuthorPrimary();
        $record['vf1_author2'] = $this->lidoRecord->getAuthorSecondary();
        $record['vf1_author2-role'] = $this->lidoRecord->getAuthorRoles();
        // Collection
        $record['collection']
            = $this->lidoRecord->getRelatedWorkDisplayObject(['relatedWork']);
        // Geographic
        $record['geographic']
            = $record['geographic_facet']
            = $this->lidoRecord->getRepositoryNamePlaceSet();
        // Institution
        $record['institution'] = $this->lidoRecord->getInstitution();
        // Recordtype
        $record['recordtype'] = $this->lidoRecord->getRecordType();
        // Urls
        $record['url'] = $this->lidoRecord->getRecordInfoLink();
        $record['fullrecord'] = $this->lidoRecord->toXML();
        return $this->elaborateRecord($record);
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

    /**
     * Get generated record id
     *
     * @params array $record Self-referential record id.
     *
     * @return array $record
     * @access protected
     * @throws \Exception Conventional principle declare source id at schema config.
     */
    protected function getRecordId(&$record)
    {
        if (!isset($this->config['static']['source_id'])) {
            throw new \Exception(
                'Conventional principle: Source id has to be given at '
                . '$config[static][source_id] at schema configuration to generate '
                . 'record id.' . "\n"
            );
        }
        if ('' == ($record_id = $this->lidoRecord->getLidoRecID())) {
            throw new \Exception(
                'Conventional principle: To generate an unique record id for finc '
                . 'Solr schema record needs an id. Failure: Missing given value at '
                . 'Lido xml.' . "\n"
            );
        }
        $record['id'] =
            sprintf(
                '%s-%d-%s',
                'finc', // $this->lidoRecord->getRecordType(),
                $this->config['static']['source_id'],
                Utils\BaseEncode::base64Encode($record_id)
            );

        return $record;
    }

}