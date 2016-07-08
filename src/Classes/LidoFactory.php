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

use \LidoCli\Ndl\LidoRecord;

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
     * @param string $data  XML LIDO data stream
     * @param string $cofix Collection prefix
     *
     * @static
     * @return object
     * @access public
     */
    public static function getLidoInstance($data, $cofix = null, $add = [])
     {
        if (isset($cofix) && (strlen($cofix)) > 0) {
            $class =
                'LidoCli\Classes'.'\\'.ucfirst(strtolower($cofix)) . 'LidoRecord';

            if (class_exists($class)) {
                 $obj = new $class($data, '', $cofix, $cofix);
                 return $obj;
            }
            throw new \UnexpectedValueException('No LidoRecord class: '.$class.
                ' exists. Check given parameter -s |--source');
        } else {
            return new LidoRecord($data, '', 'lido', 'lido');
        }
     }

}