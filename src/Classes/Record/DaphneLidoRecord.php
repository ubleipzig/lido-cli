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
namespace LidoCli\Classes\Record;

class DaphneLidoRecord extends LidoRecord
{
    /**
     * Get the default language used when building the Solr array
     *
     * @return string
     * @access public
     */
    public function getDefaultLanguage()
    {
        return 'de';
    }

    /**
     * Get institutions
     *
     * @return array
     * @access public
     */
    public function getInstitution()
    {
        $listInstitutions = $this->getRepositoryNameID();
        foreach ($listInstitutions as &$institution) {
            if (0 < preg_match('/^info:isil\/(.*)$/', $institution, $match)) {
                $institution = $match[1];
            }
        }
        return $listInstitutions;
    }
}