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
Pluf::loadFunction('Monitor_Shortcuts_BeansToPrometheus');

class Monitor_Views
{

    public const PX_FORMAT_KEY = '_px_format';

    public const PX_FORMAT_PROMETHEUS = 'text/prometheus';

    public const PX_FORMAT_INFLUXDB = 'text/influxdb';

    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return Pluf_Paginator
     */
    public function find($request, $match)
    {
        $content = new Pluf_Paginator(new Monitor());
        if (key_exists(self::PX_FORMAT_KEY, $request->REQUEST)) {
            switch ($request->REQUEST[self::PX_FORMAT_KEY]) {
                case self::PX_FORMAT_PROMETHEUS:
                case self::PX_FORMAT_INFLUXDB:
                    break;
                default:
                    $content->model_view = 'beans';
            }
        }
        $content->list_filters = array(
            'id',
            'name',
            'title'
        );
        $search_fields = array(
            'title',
            'description',
            'monitor',
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
        if (key_exists(self::PX_FORMAT_KEY, $request->REQUEST)) {
            switch ($request->REQUEST[self::PX_FORMAT_KEY]) {
                case self::PX_FORMAT_PROMETHEUS:
                    return Monitor_Shortcuts_convertBeanPageResponse($request, $content->render_object());
                case self::PX_FORMAT_INFLUXDB:
                    break;
                default:
                    break;
            }
        }
        return $content->render_object();
    }
}