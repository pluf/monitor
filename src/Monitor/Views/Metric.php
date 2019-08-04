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
Pluf::loadFunction('Monitor_Shortcuts_convertMetricToResponse');
Pluf::loadFunction('Monitor_Shortcuts_convertPageOfMetricsToResponse');

class Monitor_Views_Metric extends Monitor_Views_Abstract
{

    /**
     * Find monitor-metrics.
     *
     * If monitor-tag is specified in $match (through name or id of manitor-tag)
     * it works on list of metrics with specified monitor-tag else works on all monitor-metrics.
     *
     * @param Pluf_Http_Request $request
     * @param array $match
     * @return Pluf_Paginator
     */
    public function find($request, $match)
    {
        // find monitor:
        $content = new Pluf_Paginator(new Monitor_Metric());
        $content->list_filters = array(
            'id',
            'name'
        );
        $search_fields = array(
            'description',
            'name'
        );
        $sort_fields = array(
            'id',
            'name',
            'creation_date',
            'modif_dtime'
        );
        $content->sort_order = array(
            'id',
            'DESC'
        );
        $content->model_view = 'join_tag';
        $content->configure(array(), $search_fields, $sort_fields);
        $content->setFromRequest($request);
        return Monitor_Shortcuts_convertPageOfMetricsToResponse($request, $match, $content);
    }

    /**
     * Returns a monitor metric.
     * You could give metric id in the $match or give monitor (by name or id) and metric name
     * in the $match to get information of monitor metric
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @throws Exception
     * @return array
     */
    public function get($request, $match)
    {
        // Find metric
        $metric = self::fetchMetric($match);
        if (! isset($metric)) {
            throw new Pluf_HTTP_Error404('Metric not found.');
        }
        // Set the default
        return Monitor_Shortcuts_convertMetricToResponse($request, $match, $metric);
    }

}