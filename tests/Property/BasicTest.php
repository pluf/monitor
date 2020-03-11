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
namespace Pluf\Test\Property;

use Pluf\Test\TestCase;
use Monitor_Metric;
use Pluf;
use Pluf_Migration;

class BasicTest extends TestCase
{

    /**
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/config.php');
        $m = new Pluf_Migration(array_merge(Pluf::f('installed_apps'), array(
            'Test'
        )));
        $m->install();
        $m->init();
    }

    /**
     * @afterClass
     */
    public static function removeDatabses()
    {
        $m = new Pluf_Migration(array_merge(Pluf::f('installed_apps'), array(
            'Test'
        )));
        $m->unInstall();
    }

    /**
     * @test
     */
    public function getValueOfCacheble()
    {
        $property = new Monitor_Metric();
        $property->setFromFormData(array(
            'name' => 'auto_create_test_property_getValueOfCacheble',
            'cacheable' => true,
            'interval' => 10000,
            'function' => '\\Pluf\\RandomMonitor\\Monitor::random'
        ));
        $property->create();
        
        $result = $property->invoke(array(), array());
        $this->assertEquals($result, $property->invoke(array(), array()));
        $this->assertEquals($result, $property->invoke(array(), array()));
        $this->assertEquals($result, $property->invoke(array(), array()));
    }

    /**
     * @test
     */
    public function getValueOfCachebleDefaultInterval()
    {
        $property = new Monitor_Metric();
        $property->setFromFormData(array(
//             'monitor' => 'testMonitor',
            'name' => 'auto_create_test_property_getValueOfCachebleDefaultInterval',
            'cacheable' => true,
            'function' => '\\Pluf\\RandomMonitor\\Monitor::random'
        ));
        $property->create();
        
        $result = $property->invoke(array(), array());
        $result = $property->invoke(array(), array());
        $this->assertEquals($result, $property->invoke(array(), array()));
        $this->assertEquals($result, $property->invoke(array(), array()));
        $this->assertEquals($result, $property->invoke(array(), array()));
    }

    /**
     * @test
     */
    public function getValueOfNonCacheble()
    {
        $property = new Monitor_Metric();
        $property->setFromFormData(array(
//             'monitor' => 'testMonitor',
            'name' => 'auto_create_test_property_getValueOfNonCacheble',
            'cacheable' => false,
            'interval' => 10000,
            'function' => '\\Pluf\\RandomMonitor\\Monitor::random'
        ));
        $property->create();
        
        $result = $property->invoke(array(), array());
        $this->assertNotEquals($result, $property->invoke(array(), array()));
        $this->assertNotEquals($result, $property->invoke(array(), array()));
        $this->assertNotEquals($result, $property->invoke(array(), array()));
    }

    /**
     * @test
     */
    public function getValueOfNonCachebleDefault()
    {
        $property = new Monitor_Metric();
        $property->setFromFormData(array(
            'name' => 'auto_create_test_property_getValueOfNonCachebleDefault',
            'interval' => 10000,
            'function' => '\\Pluf\\RandomMonitor\\Monitor::random'
        ));
        $property->create();
        
        $result = $property->invoke(array(), array());
        $this->assertNotEquals($result, $property->invoke(array(), array()));
        $this->assertNotEquals($result, $property->invoke(array(), array()));
        $this->assertNotEquals($result, $property->invoke(array(), array()));
    }

    /**
     * @test
     */
    public function getValueOfMinimal()
    {
        $property = new Monitor_Metric();
        $property->setFromFormData(array(
            'name' => 'auto_create_test_property_getValueOfMinimal',
            'function' => '\\Pluf\\RandomMonitor\\Monitor::random'
        ));
        $property->create();
        
        $result = $property->invoke(array(), array());
        $this->assertNotEquals($result, $property->invoke(array(), array()));
        $this->assertNotEquals($result, $property->invoke(array(), array()));
        $this->assertNotEquals($result, $property->invoke(array(), array()));
    }
}