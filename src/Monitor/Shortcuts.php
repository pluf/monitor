<?php

/*
 * This file is part of Pluf Framework, a simple PHP Application Framework.
 * Copyright (C) 2010-2020 Phoinex Scholars Co. http://dpq.co.ir
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

function Monitor_Shortcuts_isPrometheusRequest($request){
    $PX_FORMAT_KEY = '_px_format';
    $PX_FORMAT_PROMETHEUS = 'text/prometheus';
//     $PX_FORMAT_INFLUXDB = 'text/influxdb';
    return array_key_exists($PX_FORMAT_KEY, $request->REQUEST) && $PX_FORMAT_PROMETHEUS == $request->REQUEST[$PX_FORMAT_KEY];
}


/**
 * Creates bean property label
 *
 * @param Monitor $bean
 */
function Monitor_Shortcuts_convertMetricToPrometheusLabel($bean)
{
    // TODO: hadi, 97-06-18: Fetch tags of metric (be get_tags_list function)
    // and add these tags as some labels to result
    $labels = $bean->jsonSerialize();
    $result = $bean->name . ' {';
    foreach ($labels as $key => $value) {
        if ($key == 'value' || $key == 'modif_dtime') {
            continue;
        }
        $result = $result . $key . '="' . $value . '",';
    }
    return $result . '} ';
}




/**
 *
 * @param Pluf_HTTP_Request $request
 * @param array $match
 * @param Monitor_Property $property
 * @return array
 */
function Monitor_Shortcuts_convertMetricToResponse($request, $match, $metric)
{
    $value = $metric->invoke($request, $match);
    // User defined format
    if (Monitor_Shortcuts_isPrometheusRequest($request)) {
        $result =
            Monitor_Shortcuts_convertMetricToPrometheusLabel($metric) . " " . //
            ($value ? $value : '0') . PHP_EOL;

        return new Pluf_HTTP_Response($result, 'text/plain');
    }

    return $metric;
}

/**
 * Converts page to response
 *
 * @param unknown $request
 * @param unknown $page
 * @return Pluf_HTTP_Response|unknown
 */
function Monitor_Shortcuts_convertPageOfMetricsToResponse($request, $match, $page)
{
    // User defined format
    if (Monitor_Shortcuts_isPrometheusRequest($request)) {
        $items = $page->fetchItems();
        $result = '';
        foreach ($items as $metric){
            $value = $metric->invoke($request, $match);
            $result =
                $result . //
                Monitor_Shortcuts_convertMetricToPrometheusLabel($metric) . " " . //
                ($value ? $value : '0') . PHP_EOL;
        }
        return new Pluf_HTTP_Response($result, 'text/plain');
    }
    return $page;
}



