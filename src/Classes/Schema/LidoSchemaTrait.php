<?php
/**
 * Lido - Lido schema trait
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

trait LidoSchemaTrait
{
    /**
     * Elaborate record with a couple of static defined tools.
     *
     * @param array $record Preprocessed record.
     *
     * @return array $record Return elaborated record
     * @access protected
     */
    protected function elaborateRecord($record)
    {
        foreach ($this->config as $task => $taskConfig) {
            $method = 'runTask' . ucfirst($task);
            if (isset($this->config[$task])
                && true === method_exists($this, $method)
            ) {
                $this->$method($record, $taskConfig);
            }
        }
        return $record;
    }

    /**
     * Get identifier of record driver means literally the source
     *
     * @retrun mixed null|string
     * @access protected
     */
    protected function getRecordIdentifier()
    {
        return (
            0 < preg_match(
                '/(\w*)LidoRecord$/',
                get_class($this->lidoRecord),
                $match
            )
        ) ? strtolower($match[1]) : null;
    }

    /**
     * Get identifier of used schema
     *
     * @return mixed null|string
     * @access protected
     */
    protected function getSchemaIdentifier()
    {
        return (
            0 < preg_match(
                '/LidoSchema(\w*)$/',
                get_class($this),
                $match
            )
        ) ? strtolower($match[1]) : null;
    }

    /**
     * Run task - copy values in new field
     *
     * @param array $record Self-referential record array
     * @param array $config Config container for copy
     *
     * @return array $record
     * @access protected
     */
    protected function runTaskCopy(&$record, $config)
    {
        if (is_array($config) && count($config) > 0) {
            foreach ($config as $field => $cp_field) {
                if (!isset($record[$field])) {
                    print_r('Notice: Record field: ' . $field . 'does not exist.'
                        . ' Copying record fields task cannot proceeded.' . "\n");

                } else {
                    if (isset($record[$cp_field])) {
                        print_r('Notice: Record field ' . $cp_field
                            . ' already existed.'
                            . ' Existing record field will be overwritten.' . "\n");
                    }
                    $record[$cp_field] = $record[$field];
                }
            }
        }
        return $record;
    }

    /**
     * Run task - move values in other field
     *
     * @param array $record Self-referential record array
     * @param array $config Config container for move
     *
     * @return array $record
     * @access protected
     */
    protected function runTaskMove(&$record, $config)
    {
        if (is_array($config) && count($config) > 0) {
            foreach ($config as $field => $mv_field) {
                if (!isset($record[$field])) {
                    print_r('Notice: Record field: ' . $field . 'does not exist.'
                        . ' Moving record fields task cannot proceeded.' . "\n");

                } else {
                    if (isset($record[$mv_field])) {
                        print_r('Notice: Record field ' . $mv_field
                            . ' already existed.'
                            . ' Existing record field will be overwritten.' . "\n");
                    }
                    $record[$mv_field] = $record[$field];
                    unset($record[$field]);
                }
            }
        }
        return $record;
    }

    /**
     * Run task - add static values to record
     *
     * @param array $record Self-referential record array
     * @param array $config Config container for static
     *
     * @return array $record
     * @access protected
     */
    protected function runTaskStatic(&$record, $config)
    {
        if (is_array($config) && count($config) > 0) {
            foreach ($config as $field => $val) {
                if (isset($record[$field])) {
                    print_r('Notice: Overwrite existing record field: ' . $field
                        . ' by schema configuration with value: ' . $val . "\n");
                }
                $record[$field] = $val;
            }
        }
        return $record;
    }

    /**
     * Run task - suppress/remove fields from record
     *
     * @param array $record Self-referential record array
     * @param array $config Config container for suppress
     *
     * @return array $record
     * @access protected
     */
    protected function runTaskSuppress(&$record, $config)
    {
        if (is_array($config) && count($config) > 0) {
            foreach ($config as $field) {
                if (isset($record[$field])) {
                    unset($record[$field]);
                }
            }
        }
        return $record;
    }


}
