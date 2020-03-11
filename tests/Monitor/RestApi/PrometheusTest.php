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
use Pluf\Test\TestCase;
use Pluf\Test\Client;

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../apps');

/**
 *
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
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
        Pluf::start(__DIR__ . '/../conf/config.php');
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
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
        $metric->function = 'Test_Monitor::random';
        if (true !== $metric->create()) {
            throw new Exception();
        }
        $mTag->setAssoc($metric);

        self::$client = new Client(array(
            array(
                'app' => 'Monitor',
                'regex' => '#^/api/v2/monitor#',
                'base' => '',
                'sub' => include 'Monitor/urls-v2.php'
            ),
            array(
                'app' => 'User',
                'regex' => '#^/api/v2/user#',
                'base' => '',
                'sub' => include 'User/urls-v2.php'
            )
        ));
    }

    /**
     *
     * @afterClass
     */
    public static function removeDatabses()
    {
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
        $m->unInstall();
    }

    /**
     *
     * @test
     */
    public function getListOfMonitorsInPromethues()
    {
        $response = self::$client->get('/api/v2/monitor/metrics', array(
            '_px_format' => 'text/prometheus'
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
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
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');

        $response = self::$client->get('/api/v2/monitor/tags/test/metrics', array(
            '_px_format' => 'text/prometheus'
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
    }


    /**
     * Getting test monitor as sample
     *
     * @test
     */
    public function getTestRandomMonitorPropertyForPromethues()
    {
        // login
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');

        $response = self::$client->get('/api/v2/monitor/tags/test/metrics/random', array(
            '_px_format' => 'text/prometheus'
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
    }
}