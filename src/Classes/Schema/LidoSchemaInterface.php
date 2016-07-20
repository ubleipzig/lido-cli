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
namespace LidoCli\Classes\Schema;

interface LidoSchemaInterface
{
    /**
     * Collector for array to import in Solr
     *
     * @return array    Returns array to import in Solr
     * @access public
     */
    public function toSolrArray();

    /**
     * Collector for json to import in Solr
     *
     * @return string    Returns json to import in Solr
     * @access public
     */
    public function toSolrJson();

}
