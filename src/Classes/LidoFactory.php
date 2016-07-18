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

class LidoFactory
{
    /**
     * LidoFactory constructor.
     *
     * @access public
     */
    public function __construct()
    {
        // void
    }

    /**
     * LidoFactory clone.
     *
     * @access public
     */
    public function __clone()
    {
        // void
    }

    /**
     * Load default or specific LIDO record class
     *
     * @param string $data XML LIDO data stream
     * @param string $filter Filter prefix
     * @param string $schema Schema prefix
     *
     * @static
     * @return object
     * @access public
     */
    public static function getLidoInstance($data, $filter = null, $schema = null)
    {
        $classFilter = (isset($filter) && (strlen($filter)) > 0)
            ? 'LidoCli\Classes' . '\\' . ucfirst(strtolower($filter)) . 'LidoRecord'
            : 'LidoCli\Classes' . '\\' . 'LidoRecord';

        if (class_exists($classFilter)) {
            $objFilter = new $classFilter($data, '', 'lido', 'lido');
        } else {
            throw new \UnexpectedValueException('No Lido filter class: ' . $classFilter .
                ' exists. Check given parameter -f |--filter');
        }

        if (isset($schema) && (strlen($schema)) > 0) {
            $classSchema =
                'LidoCli\Classes' . '\\LidoSchema' . ucfirst(strtolower($schema));

            if (class_exists($classSchema)) {
                $obj = new $classSchema($objFilter);
                 return $obj;
            }
            throw new \UnexpectedValueException('No Lido schema class: ' .
                $classSchema . ' exists. Check given parameter -s |--schema');
        } else {
            return new LidoSchema($objFilter);
        }
    }

}