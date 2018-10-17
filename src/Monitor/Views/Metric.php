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

class Monitor_Views_Metric
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
        $monitorTagId = self::fetchMonitorTagId($match);
        if ($monitorTagId) {
            $sql = new Pluf_SQL('monitor_tag_id=%s', array(
                $monitorTagId
            ));
            $content->forced_where = $sql;
        }
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
        return $content;
    }

    /**
     * Returns monitor-tag id from given information in $match.
     * $match may contain id or name of monitor-tag.
     *
     * It checks following keys:
     * - $match['tagId']: returns $match['tagId'] if exist.
     * - $match['tagName']: returns id of a tag which its name is equal with $match['tagName'].
     * - $match['tag']: If $match['tag'] is a number returns $match['tag'] as result
     * else returns id of a tag which its name is equal with $match['tag'].
     *
     * If none of mentioned values are existed or there is no tag with given name returns null.
     *
     * @param array $match
     * @return NULL|Number
     */
    private function fetchMonitorTagId($match)
    {
        // Check tagId key
        if (isset($match['tagId'])) {
            return $match['tagId'];
        }
        // Check tagName key
        if (isset($match['tagName'])) {
            $sql = new Pluf_SQL('name=%s', array(
                $match['tagName']
            ));
            $monitorTag = new Monitor_Tag();
            $monitorTag = $monitorTag->getOne($sql->gen());
            if ($monitorTag) {
                return $monitorTag->id;
            }
        }
        // Check tag key
        if (isset($match['tag'])) {
            $val = $match['tag'];
            if (is_numeric($val)) {
                return $val;
            }
            $sql = new Pluf_SQL('name=%s', array(
                $val
            ));
            $monitorTag = new Monitor_Tag();
            $monitorTag = $monitorTag->getOne($sql->gen());
            if ($monitorTag) {
                return $monitorTag->id;
            }
        }
        return null;
    }

    /**
     * Returns monitor-metric by using given information in $match.
     * $match may contains id or name of monitor-metric.
     *
     * It checks following keys:
     * - $match['metricId']: returns metric with id $match['metricId'] if exist.
     * - $match['metricName']: returns metric which its name is equal with $match['metricName'].
     * - $match['metric']: If $match['metric'] is a number returns metric with id $match['metricId'] as result
     * else returns metric which its name is equal with $match['metric'].
     *
     * If none of mentioned values are existed or there is no metric with given name returns null.
     *
     * @param array $match
     * @return NULL|Monitor_Metric
     */
    private function fetchMetric($match)
    {
        $metric = null;
        if (isset($match['metricId'])) {
            $metric = new Monitor_Metric($match['metricId']);
        }else if (isset($match['metricName'])) {
            $sql = new Pluf_SQL('name=%s', array(
                $match['metricName']
            ));
            $metric = Pluf::factory('Monitor_Metric')->getOne($sql->gen());
        }else  if (isset($match['metric'])) {
            $val = $match['metric'];
            if (is_numeric($val)) {
                $metric =  new Monitor_Metric($val);
            }else{                
                $sql = new Pluf_SQL('name=%s', array(
                    $val
                ));
                $metric = Pluf::factory('Monitor_Metric')->getOne($sql->gen());
            }
        }
        if ($metric && ! $metric->isAnonymous()) {
            return $metric;
        }
        return null;
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
        // Find monitor
        $tagId = self::fetchMonitorTagId($match);
        if (! isset($tagId)) {
            throw new Pluf_HTTP_Error404('The monitor tag is not provided or not found.');
        }
        // Find metric
        $metric = self::fetchMetric($match);
        if (! isset($metric)) {
            throw new Pluf_HTTP_Error404('Metric not found.');
        }
        // Set the default
        return Monitor_Shortcuts_convertBeanPropertyToResponse($request, $match, $metric);
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
    public function getMetric($request, $match)
    {
        // Find metric
        $metric = self::fetchMetric($match);
        if (! isset($metric)) {
            throw new Pluf_HTTP_Error404('Metric not found.');
        }
        // Set the default
        return Monitor_Shortcuts_convertBeanPropertyToResponse($request, $match, $metric);
    }
}