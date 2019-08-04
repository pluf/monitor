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
    // ******************************************* Tags
    array(
        'regex' => '#^/tags$#',
        'model' => 'Pluf_Views',
        'method' => 'findObject',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'Monitor_Tag'
        ),
        'precond' => array()
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
    array(
        'regex' => '#^/tags/(?P<tag>[^/]+)/metrics$#',
        'model' => 'Monitor_Views_Tag',
        'method' => 'getMetrics',
        'http-method' => 'GET'
    ),
    array(
        'regex' => '#^/tags/(?P<tag>[^/]+)/metrics/(?P<metric>[^/]+)$#',
        'model' => 'Monitor_Views_Tag',
        'method' => 'getMetric',
        'http-method' => 'GET'
    ),

    // ******************************************* Monitor metric
    array(
        'regex' => '#^/metrics$#',
        'model' => 'Monitor_Views_Metric',
        'method' => 'find',
        'http-method' => 'GET'
    ),
    array(
        'regex' => '#^/metrics/(?P<metric>[^/]+)$#',
        'model' => 'Monitor_Views_Metric',
        'method' => 'get',
        'http-method' => 'GET'
    ),
);