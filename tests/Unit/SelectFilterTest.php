<?php

namespace Kinetics\Tests\Unit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Kinetics\Filters\SelectFilter;
use Kinetics\Tests\TestCase;

class SelectFilterTest extends TestCase
{
    protected Builder $query;

    protected function setUp(): void
    {
        parent::setUp();

        $model = new class extends Model
        {
            protected $table = 'users';
        };
        $this->query = $model->newModelQuery();
        $this->query->setQuery(DB::table('users'));
    }

    public function test_options_formatting_associative()
    {
        $filter = SelectFilter::make('status')
            ->options(['active' => 'Active Status', 'inactive' => 'Inactive Status']);

        $array = $filter->toArray();

        $this->assertEquals([
            ['value' => 'active', 'label' => 'Active Status'],
            ['value' => 'inactive', 'label' => 'Inactive Status'],
        ], $array['meta']['options']);
    }

    public function test_options_formatting_numeric()
    {
        $filter = SelectFilter::make('status')
            ->options(['active', 'inactive']);

        $array = $filter->toArray();

        $this->assertEquals([
            ['value' => 'active', 'label' => 'active'],
            ['value' => 'inactive', 'label' => 'inactive'],
        ], $array['meta']['options']);
    }

    public function test_ignores_null_or_empty_values()
    {
        $filter = SelectFilter::make('status');

        $filter->apply($this->query, null);
        $this->assertEquals('select * from "users"', $this->query->toSql());

        $filter->apply($this->query, '');
        $this->assertEquals('select * from "users"', $this->query->toSql());

        $filter->apply($this->query, []);
        $this->assertEquals('select * from "users"', $this->query->toSql());

        $filter->apply($this->query, [null, '']);
        $this->assertEquals('select * from "users"', $this->query->toSql());
    }

    public function test_is_operator_single_value()
    {
        $filter = SelectFilter::make('status');

        $filter->apply($this->query, 'active');

        $this->assertStringContainsString('where "users"."status" = ?', $this->query->toSql());
        $this->assertEquals(['active'], $this->query->getBindings());
    }

    public function test_is_operator_array_value()
    {
        $filter = SelectFilter::make('status');

        $filter->apply($this->query, ['active', 'inactive']);

        $this->assertStringContainsString('where "users"."status" in (?, ?)', $this->query->toSql());
        $this->assertEquals(['active', 'inactive'], $this->query->getBindings());
    }

    public function test_is_not_operator()
    {
        $filter = SelectFilter::make('status');

        $filter->apply($this->query, [
            'operator' => 'is_not',
            'value' => 'deleted',
        ]);

        $this->assertStringContainsString('where "users"."status" != ?', $this->query->toSql());
        $this->assertEquals(['deleted'], $this->query->getBindings());
    }

    public function test_is_not_operator_array_value()
    {
        $filter = SelectFilter::make('status');

        $filter->apply($this->query, [
            'operator' => 'is_not',
            'value' => ['deleted', 'banned'],
        ]);

        $this->assertStringContainsString('where "users"."status" not in (?, ?)', $this->query->toSql());
        $this->assertEquals(['deleted', 'banned'], $this->query->getBindings());
    }
}
