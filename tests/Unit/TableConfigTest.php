<?php

namespace Kinetics\Tests\Unit;

use Kinetics\Support\TableConfig;
use PHPUnit\Framework\TestCase;

class TableConfigTest extends TestCase
{
    public function test_default_values_are_set()
    {
        $config = new TableConfig;

        $this->assertEquals(15, $config->defaultPerPage);
        $this->assertEquals(100, $config->maxPerPage);
        $this->assertEquals('id', $config->defaultSort);
        $this->assertEquals('desc', $config->defaultDirection);
        $this->assertFalse($config->preserveKeys);
        $this->assertEquals([10, 15, 25, 50, 100], $config->optionsPerPage);
    }

    public function test_with_overrides_values()
    {
        $config = new TableConfig;

        $newConfig = $config->with(
            defaultPerPage: 20,
            maxPerPage: 200,
            defaultSort: 'created_at',
            defaultDirection: 'asc'
        );

        $this->assertEquals(20, $newConfig->defaultPerPage);
        $this->assertEquals(200, $newConfig->maxPerPage);
        $this->assertEquals('created_at', $newConfig->defaultSort);
        $this->assertEquals('asc', $newConfig->defaultDirection);

        // original should not be modified
        $this->assertEquals(15, $config->defaultPerPage);
    }

    public function test_with_preserves_other_values()
    {
        $config = new TableConfig(preserveKeys: true, optionsPerPage: [1, 2, 3]);

        $newConfig = $config->with(defaultSort: 'name');

        $this->assertEquals('name', $newConfig->defaultSort);
        $this->assertTrue($newConfig->preserveKeys);
        $this->assertEquals([1, 2, 3], $newConfig->optionsPerPage);
    }
}
