<?php

namespace Pluf\RandomMonitor;

/**
 * Test monitor
 * @author maso
 *
 */
class Monitor
{

    /**
     * Random value monitor
     *
     * @return number
     */
    public static function random()
    {
        return rand(0, 100000);
    }
}