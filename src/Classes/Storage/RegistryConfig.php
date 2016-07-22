<?php
/**
 * Registry config class
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
namespace LidoCli\Classes\Storage;

class RegistryConfig extends Registry
{
    /**
     * Data list
     *
     * @var array $_data
     * @access private
     */
    private $_data = [];


    /**
     * Invoke class
     *
     * @access private
     * @todo Implement _invoke to load default config settings.
     */
    private function _invoke()
    {
        $this->_data = (count($this->_data) > 0) ? $this->_data : [];
    }

    /**
     * Get data as array.
     *
     * @return array
     * @access public
     */
    public function getArray()
    {
        return $this->_data;
    }

    /**
     * Load schema configuration array optionally for source
     *
     * @param string $schema Schema identifier
     * @param string $source Source identifier
     *
     * @return object $this
     * @access public
     */
    public function loadSchema($schema = null, $source = null)
    {
        $configPathToFile =
            __DIR__ . "/../../../config/" . $schema . ".schema.php";
        if (false === file_exists($configPathToFile)) {
            return $this;
        }
        $config = require $configPathToFile;
        if (null == $source || !isset($config[$source])) {
            $this->_data = array_merge($this->_data, $config);
        } else {
            $this->_data = array_merge($this->_data, $config[$source]);
        }
        return $this;
    }
}