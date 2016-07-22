<?php
/**
 *
 * Registry pattern class
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

abstract class Registry
{
    /**
     * Static var instances
     *
     * @staticvar array $_instances
     * @access protected
     */
    protected static $_instances = [];

    /**
     * Get Instance
     *
     * @return instance
     * @access public
     * @static
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$_instances[$class])
        ) {
            self::$_instances[$class] = new $class;
        }
        return self::$_instances[$class];
    }

    /**
     * Constructor - overridden by some subclasses
     *
     * @access protected
     */
    private function __construct()
    {
        // void body
    }

    /**
     * Clone - prevent cloning instance of the registry
     *
     * @access private
     */
    private function __clone()
    {
        // void body
    }

    /**
     * Set method - implemented by subclasses
     *
     * @access public
     * @abstract
     */
    //abstract public function set($key, $value);

    /**
     * Get method - implemented by subclasses
     *
     * @access public
     * @abstract
     */
    //abstract public function get($key);

    /**
     * Clear method - implemented by subclasses
     *
     * @access public
     * @abstract
     */
    //abstract public function clear();
}

