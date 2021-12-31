<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WorklogTest extends TestCase
{
    /**
     * Check if the enpoint exist
     *
     * @return void
     */
    public function test_checkEndpointExistence()
    {
        $response = $this->post('/api/work-logs/login');

        $response->assertStatus(404);
    }

    /**
     * Create login record
     *
     * @return void
     */
    public function test_createLoginRecord()
    {
        $response = $this->post('/api/work-logs/login', ['user_id' => 1, 'record_date' => '2020-01-01', 'start_time' => '10:15:00']);

        $response->assertStatus(200);
    }

    /**
     * Try to create invalid login record
     *
     * @return void
     */
    public function test_createInvalidLoginRecord()
    {
        $response = $this->post('/api/work-logs/login', ['user_id' => 1, 'record_date' => '2020-01-01', 'start_time' => '10:15:00']);

        $response->assertStatus(400);
    }

    /**
     * Try to create logout record
     *
     * @return void
     */
    public function test_createLogoutRecord()
    {
        $response = $this->post('/api/work-logs/logout', ['user_id' => 1, 'end_time' => '10:17:00']);

        $response->assertStatus(200);
    }

    /**
     * get billable hours For an Invalid Project
     *
     * @return void
     */
    public function test_getBillableHoursForInvalidProject()
    {
        $response = $this->post('/api/reports/projects/billable-hours', ['project_id' => 10000]);

        $response->assertStatus(400);
    }

    /**
     * get billable hours For a valid Project
     *
     * @return void
     */
    public function test_getBillableHoursForValidProject()
    {
        $response = $this->post('/api/reports/projects/billable-hours', ['project_id' => 1]);

        $response->assertStatus(200);
    }

    /**
     * get Peak Time For an Invalid Project
     *
     * @return void
     */
    public function test_getPeaktimeForInvalidProject()
    {
        $response = $this->post('/api/reports/projects/getpeak-time', ['project_id' => 10000, 'record_date' => '2020-01-01']);

        $response->assertStatus(400);
    }

     /**
     * Create login record for User 2
     *
     * @return void
     */
    public function test_createLoginRecordForAnotherUser()
    {
        $response = $this->post('/api/work-logs/login', ['user_id' => 2, 'record_date' => '2020-01-01', 'start_time' => '10:15:00']);

        $response->assertStatus(200);
    }
    
    /**
     * Try to create logout record For User 2
     *
     * @return void
     */
    public function test_createLogoutRecordForAotherUser()
    {
        $response = $this->post('/api/work-logs/logout', ['user_id' => 2, 'end_time' => '10:17:00']);

        $response->assertStatus(200);
    }

    /**
     * get Peak Time For a valid Project
     *
     * @return void
     */
    public function test_getPeaktimeForvalidProject()
    {
        $response = $this->post('/api/reports/projects/getpeak-time', ['project_id' => 1, 'record_date' => '2020-01-01']);

        $response->assertStatus(200);
    }
}
