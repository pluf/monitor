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

    public function find($request, $match)
    {
        // find monitor:
        $content = new Pluf_Paginator(new Monitor_Property());
        $sql = new Pluf_SQL('monitor=%s', array(
            $match['monitor']
        ));
        $content->forced_where = $sql;
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

    public static function get($request, $match)
    {
        // Find monitor
        if (isset($match['monitorId'])) {
            $monitorId = $match['monitorId'];
        } else if (isset($match['monitor'])) {
            $sql = new Pluf_SQL('name=%s', array(
                $match['monitor']
            ));
            $monitor = new Monitor();
            $monitor = $monitor->getOne($sql->gen());
            if(!$monitor){
                throw new Pluf_Exception_DoesNotExist('Monitor not found :' . $match['monitor']);
            }
            $monitorId = $monitor->id;
        }
        
        // Find property
        if (isset($match['propertyId'])) {
            $property = new Monitor_Property($match['propertyId']);
        } else if (isset($match['property'])) {
            if(!isset($monitorId)) {
                throw new Exception('The monitor was not provided in the parameters.');
            }
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
        $result = array_merge($property->_data, $request);
        return $result;
    }
}