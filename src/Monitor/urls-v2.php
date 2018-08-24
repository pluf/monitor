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
return array(
    // ******************************************* Monitor tag
    array(
        'regex' => '#^/tags$#',
        'model' => 'Monitor_Views_Tag',
        'method' => 'find',
        'http-method' => 'GET'
    ),
    array(
        'regex' => '#^/tags/(?P<modelId>\d+)$#',
        'model' => 'Pluf_Views',
        'method' => 'getObject',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'Monitor_Tag'
        ),
        'precond' => array()
    ),
    // ******************************************* metrics of tag
//     array(
//         'regex' => '#^/tags/(?P<tagId>\d+)/metrics$#',
//         'model' => 'Monitor_Views_Metric',
//         'method' => 'find',
//         'http-method' => 'GET'
//     ),
//     array(
//         'regex' => '#^/tags/(?P<tagName>[^/]+)/metrics$#',
//         'model' => 'Monitor_Views_Metric',
//         'method' => 'find',
//         'http-method' => 'GET'
//     ),
    array(
        'regex' => '#^/tags/(?P<tag>[^/]+)/metrics$#',
        'model' => 'Monitor_Views_Metric',
        'method' => 'find',
        'http-method' => 'GET'
    ),
//     array(
//         'regex' => '#^/tags/(?P<tagId>\d+)/metrics/(?P<metricId>\d+)$#',
//         'model' => 'Monitor_Views_Metric',
//         'method' => 'get',
//         'http-method' => 'GET'
//     ),
//     array(
//         'regex' => '#^/tags/(?P<tagId>\d+)/metrics/(?P<metricName>[^/]+)$#',
//         'model' => 'Monitor_Views_Metric',
//         'method' => 'get',
//         'http-method' => 'GET'
//     ),
//     array(
//         'regex' => '#^/tags/(?P<tagName>[^/]+)/metrics/(?P<metricId>\d+)$#',
//         'model' => 'Monitor_Views_Metric',
//         'method' => 'get',
//         'http-method' => 'GET'
//     ),
//     array(
//         'regex' => '#^/tags/(?P<tagName>[^/]+)/metrics/(?P<metricName>[^/]+)$#',
//         'model' => 'Monitor_Views_Metric',
//         'method' => 'get',
//         'http-method' => 'GET'
//     ),
    array(
        'regex' => '#^/tags/(?P<tag>[^/]+)/metrics/(?P<metric>[^/]+)$#',
        'model' => 'Monitor_Views_Metric',
        'method' => 'get',
        'http-method' => 'GET'
    ),
    // ******************************************* Monitor metric
    array(
        'regex' => '#^/metrics$#',
        'model' => 'Pluf_Views',
        'method' => 'findObject',
        'http-method' => 'GET',
        'precond' => array(),
        'params' => array(
            'model' => 'Monitor_Metric',
            'sortOrder' => array(
                'id',
                'DESC'
            )
        )
    ),
//     array(
//         'regex' => '#^/metrics/(?P<metricId>\d+)$#',
//         'model' => 'Monitor_Views_Metric',
//         'method' => 'getMetric',
//         'http-method' => 'GET'
//     ),
//     array(
//         'regex' => '#^/metrics/(?P<metricName>[^/]+)$#',
//         'model' => 'Monitor_Views_Metric',
//         'method' => 'getMetric',
//         'http-method' => 'GET'
//     ),
    array(
        'regex' => '#^/metrics/(?P<metric>[^/]+)$#',
        'model' => 'Monitor_Views_Metric',
        'method' => 'getMetric',
        'http-method' => 'GET'
    ),
    
    // ******************************************* Old versions
    array(
        'regex' => '#^/tags/(?P<tagName>[^/]+)/metrics$#',
        'model' => 'Monitor_Views_Metric',
        'method' => 'find',
        'http-method' => 'GET'
    ),
    array(
        'regex' => '#^/(?P<tagName>[^/]+)/(?P<metricName>[^/]+)$#',
        'model' => 'Monitor_Views_Metric',
        'method' => 'get',
        'http-method' => 'GET'
    )
);