<?php
/*
 * This file is part of Pluf Framework, a simple PHP Application Framework.
 * Copyright (C) 2010-2020 Phoinex Scholars Co. (http://dpq.co.ir)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
Pluf::loadFunction('Monitor_Shortcuts_UserLevel');

class Monitor_Views_Property
{

    /**
     * Find monitor-properties. 
     * 
     * If monitor is specified in $match (through name or id of manitor)
     * it works on list of properties of specified monitor else works on all monitor properties.
     * 
     * @param Pluf_Http_Request $request
     * @param array $match
     * @return Pluf_Paginator
     */
    public function find($request, $match)
    {
        // find monitor:
        $content = new Pluf_Paginator(new Monitor_Property());
        $monitorId = Monitor_Views_Property::fetchMonitorId($match);
        if ($monitorId) {
            $sql = new Pluf_SQL('monitor=%s', array(
                $monitorId
            ));
            $content->forced_where = $sql;
        }
        $content->list_filters = array(
            'id',
            'monitor',
            'name',
            'title'
        );
        $search_fields = array(
            'title',
            'description',
            'name'
        );
        $sort_fields = array(
            'id',
            'name',
            'title',
            'monitor',
            'creation_date',
            'modif_dtime'
        );
        $content->sort_order = array(
            'id',
            'DESC'
        );
        $content->configure(array(), $search_fields, $sort_fields);
        $content->setFromRequest($request);
        return $content->render_object();
    }
    
    /**
     * Returns monitor id from given information in $match. $match may contain id or name of monitor.
     * @param array $match
     * @return NULL|Number
     */

    private static function fetchMonitorId($match)
    {
        if (isset($match['monitorId'])) {
            return $match['monitorId'];
        } 
        
        if (isset($match['monitor'])) {
            $sql = new Pluf_SQL('name=%s', array(
                $match['monitor']
            ));
            $monitor = new Monitor();
            $monitor = $monitor->getOne($sql->gen());
            if (! $monitor) {
                return null;
            }
            return $monitor->id;
        }
        return null;
    }

    /**
     * Returns a monitor property. You could give property id in the $match or give monitor (by name or id) and property name
     * in the $match to get information of monitor property
     * 
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @throws Exception
     * @return array
     */
    public static function get($request, $match)
    {
        // Find monitor
        $monitorId = self::fetchMonitorId($match);
        if (! isset($monitorId)) {
            throw new Pluf_Exception_GetMethodSuported('The monitor was not provided in the parameters.');
        }
        
        // Find property
        if (isset($match['propertyId'])) {
            $property = new Monitor_Property($match['propertyId']);
        } else if (isset($match['property'])) {
            $sql = new Pluf_SQL('name=%s AND monitor=%s', array(
                $match['property'],
                $monitorId
            ));
            $property = Pluf::factory('Monitor_Property')->getOne($sql->gen());
        } else {
            throw new Exception('The property was not provided in the parameters.');
        }
        // Set the default
        $result = $property->invoke($request, $match);
        $result = array_merge($property->jsonSerialize(), array('value' => $result));
        return $result;
    }
}