<?php
/**
 * Lido - Lido Basic Record class extends LIDO Records class of NatLibFi
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

use \LidoCli\Ndl\LidoRecord as NativeLidoRecord;
use \LidoCli\Ndl\MetadataUtils;

class LidoRecord extends NativeLidoRecord
{
    /**
     * Actors container list
     *
     * @var mixed null|array $actors
     * @æccess protected
     *
     */
    protected $actors = null;

    /**
     * Return names, roles and identifier of actors associated with specified event.
     *
     * @param string|string[] $event Which events to use (omit to scan all events)
     * @param string|string[] $role Which roles to use (omit to scan all roles)
     *
     * @return array
     * @access public
     */
    public function getActors($event = null, $role = null)
    {
        $result = [];
        $i = 0;
        foreach ($this->getEventNodes($event) as $eventNode) {
            foreach ($eventNode->eventActor as $actorNode) {
                $result[$i]['displayActorSet'] =
                    (string)$actorNode->displayActorInRow;
                foreach ($actorNode->actorInRole as $roleNode) {
                    if (isset($roleNode->actor->nameActorSet->appellationValue)) {
                        $result[$i]['nameActorSet'] =
                            (string)$roleNode->actor->nameActorSet
                                ->appellationValue[0];
                        $result[$i]['roleActor'] =
                            (string)$roleNode->roleActor->term;
                        $result[$i]['actorID'] =
                            $this->filterActorID(
                                (string)$roleNode->actor->actorID,
                                'gnd'
                            );
                    }
                }
                $i++;
            }
        }
        return $result;
    }

    /**
     * Get actors instance
     *
     * @return array $actors    Return instance with actors array.
     * @access protected
     */
    protected function getActorsInstance()
    {
        return $this->actors = ($this->actors == null)
            ? $this->getActors() : $this->actors;
    }

    /**
     * Get author identifier like gnd
     *
     * @return array
     * @access public
     */
    public function getAuthorIds()
    {
        return array_unique(
            array_map(
                function ($arr) {
                    return $arr['actorID'];
                },
                $this->getActorsInstance()
            )
        );
    }

    /**
     * Get primary author
     *
     * @return string
     * @access public
     */
    public function getAuthorPrimary()
    {
        if (count($this->getActorsInstance()) > 0) {
            $authors = array_unique(
                array_map(function ($arr) {
                    return $arr['nameActorSet'];
                }, $this->getActorsInstance())
            );
            return $authors[0];
        }
        return [];
    }

    /**
     * Get author roles
     *
     * @return array
     * @access public
     */
    public function getAuthorRoles()
    {
        return array_unique(
            array_map(function ($arr) {
                return $arr['roleActor'];
            }, $this->getActorsInstance()),
            SORT_STRING
        );
    }

    /**
     * Get all authors
     *
     * @return array
     * @access public
     */
    public function getAuthors()
    {
        return array_unique(
            array_map(function ($arr) {
                return $arr['nameActorSet'];
            }, $this->getActorsInstance())
        );
    }

    /**
     * Get secondary authors
     *
     * @return array
     * @access public
     */
    public function getAuthorSecondary()
    {
        if (count($this->getActorsInstance()) > 1) {
            $authors = array_unique(
                array_map(function ($arr) {
                    return $arr['nameActorSet'];
                }, $this->getActorsInstance())
            );
            unset($authors[0]);
            return $authors;
        }
        return [];
    }

    /**
     * Return the date range associated with specified event
     *
     * @param string $event Which event to use (omit to scan all events)
     *
     * @return null|string[] Null if parsing failed, two ISO 8601 dates otherwise
     * @access public
     */
    public function getDateRange($event = null)
    {
        $startDate = '';
        $endDate = '';
        $displayDate = '';
        $periodName = '';
        foreach ($this->getEventNodes($event) as $eventNode) {
            if (!$startDate
                && !empty($eventNode->eventDate->date->earliestDate)
                && !empty($eventNode->eventDate->date->latestDate)
            ) {
                $startDate = (string)$eventNode->eventDate->date->earliestDate;
                $endDate = (string)$eventNode->eventDate->date->latestDate;
                break;
            }
            if (!$displayDate && !empty($eventNode->eventDate->displayDate)) {
                $displayDate = (string)$eventNode->eventDate->displayDate;
            }
            if (!$periodName && !empty($eventNode->periodName->term)) {
                $periodName = (string)$eventNode->periodName->term;
            }
        }

        return $this->processDateRangeValues(
            $startDate,
            $endDate,
            $displayDate,
            $periodName
        );
    }

    /**
     * Convert a date range to multiple string field in Solr
     *
     * @param array $range Start and end date
     *
     * @return array Filled array with numbers of years between start and end date.
     */
    public function getDateRangeForStringType($range)
    {
        if (empty($range[0]) || empty($range[1])) {
            return [];
        }
        return range(substr($range[0], 0, 4), substr($range[1], 0, 4), 1);
    }

    /**
     * Get institutions
     *
     * @return array
     * @access public
     */
    public function getInstitution()
    {
        return [];
    }

    /**
     * Return the legal body ID.
     *
     * @link   http://www.lido-schema.org/schema/v1.0/lido-v1.0-schema-listing.html
     * #legalBodyRefComplexType
     * @return string
     * @access protected
     */
    protected function getLegalBodyId()
    {
        $empty = empty($this->doc->lido->descriptiveMetadata
            ->objectIdentificationWrap->repositoryWrap->repositorySet);
        if ($empty) {
            return [];
        }
        $listBodyID = [];
        foreach ($this->doc->lido->descriptiveMetadata->objectIdentificationWrap
                     ->repositoryWrap->repositorySet as $set
        ) {
            if (!empty($set->repositoryName->legalBodyID)) {
                $listBodyID[] = (string)$set->repositoryName->legalBodyID;
            }
        }
        return $listBodyID;
    }

    /**
     * Return unique value as year for publish date sort.
     *
     * @param string $dateString
     *
     * @return int      Integer as year
     * @access public
     */
    public function getPublishDateSort($dateString)
    {
        return MetadataUtils::extractYear($dateString);
    }

    /**
     * Return record info link.
     *
     * @link   http://www.lido-schema.org/schema/v1.0/lido-v1.0-schema-listing.html
     * #legalBodyRefComplexType
     * @return string
     * @access public
     */
    public function getRecordInfoLink()
    {
        $empty = empty($this->doc->lido->administrativeMetadata->recordWrap
            ->recordInfoSet->recordInfoLink);
        if ($empty) {
            return '';
        }
        return (string)$this->doc->lido->administrativeMetadata->recordWrap
            ->recordInfoSet->recordInfoLink;
    }

    /**
     * Get record type identifier
     *
     * @return string
     * @access protected
     */
    public function getRecordType()
    {
        return 'lido';
    }

    /**
     * Return the collection of the object.
     *
     * @param string[] $relatedWorkRelType Which relation types to use
     *
     * @return string
     * @access public
     */
    public function getRelatedWorkDisplayObject($relatedWorkRelType)
    {
        return parent::getRelatedWorkDisplayObject($relatedWorkRelType);
    }

    /**
     * Get source identifier
     *
     * @return string
     * @access public
     */
    public function getSourceId()
    {
        return '';
    }

    /**
     * Filter actor id for given scheme
     *
     * @params string $text     Text to filter
     * @params string $scheme   Possible schemes are (gnd|...)
     *
     * @return string $text
     * @access protected
     */
    protected function filterActorID($text, $scheme)
    {
        if (strtolower($scheme) == 'gnd') {
            $match = [];
            return (
                0 < preg_match(
                    "/^http:\/\/d-nb.info\/gnd\/(.*)$/",
                    trim($text),
                    $match
                )
            ) ? $match[1] : $text;
        }
        return $text;
    }

    /**
     * Process extracted date values and create best possible date range
     *
     * @param string $startDate Start date
     * @param string $endDate End date
     * @param string $displayDate Display date
     * @param string $periodName Period name
     *
     * @return null|string[] Null if parsing failed, two ISO 8601 dates otherwise
     * @throws \Exception Invalid date range
     */
    protected function processDateRangeValues(
        $startDate,
        $endDate,
        $displayDate,
        $periodName
    )
    {
        if ($startDate) {
            if ($endDate < $startDate) {
                throw new \Exception(
                    "Invalid date range {$startDate} - {$endDate}, record "
                    . "{$this->source}." . $this->getID()
                );
                $endDate = $startDate;
            }
            $startDate = $this->completeDate($startDate);
            $endDate = $this->completeDate($endDate, true);
            if ($startDate === null || $endDate === null) {
                return null;
            }

            return [$startDate, $endDate];
        }

        if ($displayDate) {
            //return $this->parseDateRange($displayDate);
            return $displayDate;
        }
        if ($periodName) {
            //return $this->parseDateRange($periodName);
            return $periodName;
        }
        return null;
    }

    /**
     * Complete a partial date
     *
     * @param string $date Date string
     * @param bool $end Whether $date represents the end of a date range
     *
     * @return null|string
     * @access protected
     */
    protected function completeDate($date, $end = false)
    {
        $negative = false;
        if (substr($date, 0, 1) == '-') {
            $negative = true;
            $date = substr($date, 1);
        }

        if (!$end) {
            if (strlen($date) == 1) {
                $date = '000' . $date . '-01-01T00:00:00Z';
            } elseif (strlen($date) == 2) {
                $date = '00' . $date . '-01-01T00:00:00Z';
            } elseif (strlen($date) == 3) {
                $date = '0' . $date . '-01-01T00:00:00Z';
            } elseif (strlen($date) == 4) {
                $date = $date . '-01-01T00:00:00Z';
            } elseif (strlen($date) == 7) {
                $date = $date . '-01T00:00:00Z';
            } elseif (strlen($date) == 10) {
                $date = $date . 'T00:00:00Z';
            }
        } else {
            if (strlen($date) == 1) {
                $date = '00' . $date . '-12-31T23:59:59Z';
            } elseif (strlen($date) == 2) {
                $date = '00' . $date . '-12-31T23:59:59Z';
            } elseif (strlen($date) == 3) {
                $date = '0' . $date . '-12-31T23:59:59Z';
            } elseif (strlen($date) == 4) {
                $date = $date . '-12-31T23:59:59Z';
            } elseif (strlen($date) == 7) {
                $d = new DateTime($date . '-01');
                $date = $d->format('Y-m-t') . 'T23:59:59Z';
            } elseif (strlen($date) == 10) {
                $date = $date . 'T23:59:59Z';
            }
        }
        if ($negative) {
            $date = "-$date";
        }
        return $date;
    }


}