<?php

class Monitor_Views_Abstract {

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
    protected function fetchMonitorTagId($match)
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
    protected function fetchMetric($match)
    {
        $metric = null;
        if (isset($match['metricId'])) {
            $metric = new Monitor_Metric($match['metricId']);
        } else if (isset($match['metricName'])) {
            $sql = new Pluf_SQL('name=%s', array(
                $match['metricName']
            ));
            $metric = Pluf::factory('Monitor_Metric')->getOne($sql->gen());
        } else if (isset($match['metric'])) {
            $val = $match['metric'];
            if (is_numeric($val)) {
                $metric = new Monitor_Metric($val);
            } else {
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
}