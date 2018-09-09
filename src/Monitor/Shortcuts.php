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

/**
 *
 * @param Pluf_HTTP_Request $request
 * @param array $match
 * @param Monitor_Property $property
 * @return array
 */
function Monitor_Shortcuts_convertBeanPropertyToResponse($request, $match, $property)
{
    $value = $property->invoke($request, $match);
    // User defined format
    if (array_key_exists('_px_format', $request->REQUEST) && 'text/prometheus' == $request->REQUEST['_px_format']) {
        $result = Monitor_SHortcuts_BeanPropertyToPrometheusFormat($property, $value);
        return new Pluf_HTTP_Response($result, 'text/plain');
    }

    // default
    return $property;
}

function Monitor_Shortcuts_convertBeanPageResponse($request, $page)
{
    $result = '';
    foreach ($page['items'] as $bean) {
        $result = $result . Monitor_Shortcuts_BeanPropertiesToPrometheus($request, $bean);
    }
    return new Pluf_HTTP_Response($result, 'text/plain');
}

// ************************************************************* private *******************
/**
 * Return monitor level
 *
 * @param Pluf_HTTP_Request $request
 * @throws Pluf_HTTP_Error403
 * @return number
 */
function Monitor_Shortcuts_UserLevel($request)
{
    $user = $request->user;
    if ($user->isAnonymous() || ! $user->active) {
        return 100;
    }
    if ($user->hasPerm('Pluf::owner')) {
        return 2;
    }
    if ($user->hasPerm('Pluf::member')) {
        return 3;
    }
    if ($user->hasPerm('Pluf::authorized')) {
        return 4;
    }
}

/**
 * Converts beans into a Prometheus respons
 *
 * @param Monitor[] $beans
 * @param Pluf_HTTP_Request $request
 * @param array $match
 * @return Pluf_HTTP_Response
 */
function Monitor_Shortcuts_BeansToPrometheus($beans, $request, $match)
{
    return Monitor_Shortcuts_BeanPropertiesToPrometheus($beans, $request, $match);
}

function Monitor_Shortcuts_BeanPropertiesToPrometheus($request, $bean)
{
    $properties = $bean->get_properties_list();
    $result = '';
    foreach ($properties as $property) {
        $value = $property->invoke($request);
        $result = $result . Monitor_SHortcuts_BeanPropertyToPrometheusFormat($property, $value);
    }
    return $result;
}

/**
 * Creates bean property label
 *
 * @param Monitor $bean
 */
function Monitor_Shortcuts_BeansToPrometheusLabel($bean)
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
 * Convert a property into a text
 *
 * @param Monitor_Property $property
 * @param array $value
 * @return string
 */
function Monitor_SHortcuts_BeanPropertyToPrometheusFormat($property, $value)
{
    return Monitor_Shortcuts_BeansToPrometheusLabel($property) . " " . ($value ? $value : '0') . PHP_EOL;
}
