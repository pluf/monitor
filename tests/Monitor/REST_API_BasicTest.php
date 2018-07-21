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
use PHPUnit\Framework\TestCase;
require_once 'Pluf.php';

/**
 *
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class Monitor_REST_API_BasicTest extends TestCase
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

        $user = new User();
        $user->login = 'test';
        $user->first_name = 'test';
        $user->last_name = 'test';
        $user->email = 'toto@example.com';
        $user->setPassword('test');
        $user->active = true;
        $user->administrator = true;
        if (true !== $user->create()) {
            throw new Exception();
        }

        $role = Role::getFromString('Pluf.owner');
        $user->setAssoc($role);

        self::$client = new Test_Client(array(
            array(
                'app' => 'Monitor',
                'regex' => '#^/api/monitor#',
                'base' => '',
                'sub' => include 'Monitor/urls.php'
            ),
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
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
    public function getListOfMonitors()
    {
        // login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');

        $response = self::$client->get('/api/monitor/find');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponsePaginateList($response, 'Find result is not JSON paginated list');
        Test_Assert::assertResponseNonEmptyPaginateList($response, 'Monitor list is empty?!');
    }

    /**
     * Getting owner monitor as sample
     *
     * @test
     */
    public function getOwnerMonitor()
    {
        // login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');

        $response = self::$client->get('/api/monitor/user/property/owner');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponseAsModel($response, 'Is not a valid model');
    }

    /**
     * Getting owner monitor as sample
     *
     * @test
     */
    public function getOwnerMonitorPropertyForPromethues()
    {
        // login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');

        $response = self::$client->get('/api/monitor/user/property/owner', array(
            '_px_format' => 'text/prometheus'
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
    }

    /**
     * Getting all monitors as sample for prometheus
     *
     * @test
     */
    public function getMonitorsForPromethues()
    {
        // login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');

        $response = self::$client->get('/api/monitor/find', array(
            '_px_format' => 'text/prometheus'
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
    }
}