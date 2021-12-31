<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /**
     * Test get Duration Time In Minute
     *
     * @return void
     */
    public function test_getDurationTimeInMinute()
    {
        $result = getDurationTimeInMinute('10:07:00', '10:10:00');
        $this->assertEquals($result, 3);
    }
}
