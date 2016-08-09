<?php
/**
 * Lido - Util for en- and decoding of base standards
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
namespace LidoCli\Classes\Utils;

class BaseEncode
{
    /**
     * Decode a base64 string due to RFC 4648.
     *
     * @param  string $data
     * @return int
     * @access public
     */
    public static function base64Decode($data)
    {
        return base64_decode(str_replace(array('-', '_'), array('+', '/'), $data));
    }

    /**
     * Encode a base64 string due to to RFC 4648.
     *
     * @param  string $data
     * @param  int $pad
     * @return string
     * @access public
     */
    public static function base64Encode($data, $pad = null)
    {
        $data = str_replace(array('+', '/'), array('-', '_'), base64_encode($data));
        if (!$pad) {
            $data = rtrim($data, '=');
        }
        return $data;
    }
}