<?php

namespace Kinetics\Tests\Unit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Kinetics\Filters\TextFilter;
use Kinetics\Tests\TestCase;

class TextFilterTest extends TestCase
{
    protected Builder $query;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock a basic query builder
        $model = new class extends Model
        {
            protected $table = 'users';
        };
        $this->query = $model->newModelQuery();
        $this->query->setQuery(DB::table('users'));
    }

    public function test_ignores_null_or_empty_values()
    {
        $filter = TextFilter::make('name');

        $filter->apply($this->query, null);
        $this->assertEquals('select * from "users"', $this->query->toSql());

        $filter->apply($this->query, '');
        $this->assertEquals('select * from "users"', $this->query->toSql());
    }

    public function test_default_operator_is_contains()
    {
        $filter = TextFilter::make('name');

        // payload is just the value
        $filter->apply($this->query, 'John');

        $this->assertStringContainsString('like', $this->query->toSql());
        $this->assertEquals(['%John%'], $this->query->getBindings());
    }

    public function test_equals_operator()
    {
        $filter = TextFilter::make('name');

        $filter->apply($this->query, [
            'operator' => 'equals',
            'value' => 'John',
        ]);

        $this->assertStringContainsString('where "users"."name" = ?', $this->query->toSql());
        $this->assertEquals(['John'], $this->query->getBindings());
    }

    public function test_starts_with_operator()
    {
        $filter = TextFilter::make('name');

        $filter->apply($this->query, [
            'operator' => 'starts_with',
            'value' => 'John',
        ]);

        $this->assertStringContainsString('like', $this->query->toSql());
        $this->assertEquals(['John%'], $this->query->getBindings());
    }

    public function test_ends_with_operator()
    {
        $filter = TextFilter::make('name');

        $filter->apply($this->query, [
            'operator' => 'ends_with',
            'value' => 'John',
        ]);

        $this->assertStringContainsString('like', $this->query->toSql());
        $this->assertEquals(['%John'], $this->query->getBindings());
    }
}
