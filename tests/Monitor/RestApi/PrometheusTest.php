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
namespace Pluf\Test\Monitor\RestApi;

use Pluf\Exception;
use Pluf\Test\Client;
use Pluf\Test\TestCase;
use Monitor_Metric;
use Monitor_Tag;
use Pluf;
use Pluf_Migration;
use User_Account;
use User_Credential;
use User_Role;

class PrometheusTest extends TestCase
{

    private static $client = null;

    private static $user = null;

    /**
     *
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../../conf/config.php');
        $m = new Pluf_Migration();
        $m->install();
        $m->init();

        // Test user
        $user = new User_Account();
        $user->login = 'test';
        $user->is_active = true;
        if (true !== $user->create()) {
            throw new Exception();
        }
        // Credential of user
        $credit = new User_Credential();
        $credit->setFromFormData(array(
            'account_id' => $user->id
        ));
        $credit->setPassword('test');
        if (true !== $credit->create()) {
            throw new Exception();
        }

        $per = User_Role::getFromString('tenant.owner');
        $user->setAssoc($per);

        $mTag = new Monitor_Tag();
        $mTag->name = 'test';
        $mTag->description = 'It is a test monitor tag';
        if (true !== $mTag->create()) {
            throw new Exception();
        }
        $metric = new Monitor_Metric();
        $metric->name = 'random';
        $metric->description = 'It is a test random monitor metric';
        $metric->function = '\\Pluf\\RandomMonitor\\Monitor::random';
        if (true !== $metric->create()) {
            throw new Exception();
        }
        $mTag->setAssoc($metric);

        self::$client = new Client();
    }

    /**
     *
     * @afterClass
     */
    public static function removeDatabses()
    {
        $m = new Pluf_Migration();
        $m->unInstall();
    }

    /**
     *
     * @test
     */
    public function getListOfMonitorsInPromethues()
    {
        $response = self::$client->get('/monitor/metrics', array(
            '_px_format' => 'text/prometheus'
        ));
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        // TODO: maso, 2019: check if this is prometheus value
    }


    /**
     * Getting test monitor as sample
     *
     * @test
     */
    public function getTestMonitorsPropertyForPromethues()
    {
        // login
        $response = self::$client->post('/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertResponseStatusCode($response, 200, 'Fail to login');

        $response = self::$client->get('/monitor/tags/test/metrics', array(
            '_px_format' => 'text/prometheus'
        ));
        $this->assertResponseNotNull($response, 'Find result is empty');
    }


    /**
     * Getting test monitor as sample
     *
     * @test
     */
    public function getTestRandomMonitorPropertyForPromethues()
    {
        // login
        $response = self::$client->post('/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertResponseStatusCode($response, 200, 'Fail to login');

        $response = self::$client->get('/monitor/tags/test/metrics/random', array(
            '_px_format' => 'text/prometheus'
        ));
        $this->assertResponseNotNull($response, 'Find result is empty');
    }
}