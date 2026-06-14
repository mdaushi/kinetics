<?php

namespace Kinetics\Tests\Unit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Kinetics\Columns\TextColumn;
use Kinetics\Pipes\SortPipe;
use Kinetics\Support\TableConfig;
use Kinetics\Support\TableContext;
use Kinetics\Tests\Feature\TestUser;
use Kinetics\Tests\TestCase; // use the dummy model in Feature tests

class SortPipeTest extends TestCase
{
    protected Builder $query;

    protected function setUp(): void
    {
        parent::setUp();

        $model = new TestUser;
        $this->query = $model->newModelQuery();
    }

    public function test_sort_pipe_passes_through_if_no_context()
    {
        $pipe = new SortPipe;

        $pipe->handle($this->query, function ($q) {
            return $q;
        });

        // Query should be unmodified
        $this->assertStringNotContainsString('order by', $this->query->toSql());
    }

    public function test_sort_pipe_applies_local_column_sort()
    {
        $request = new Request(['sort' => 'name', 'direction' => 'desc']);
        $config = new TableConfig;
        $context = new TableContext($this->query, $request, $config, [
            TextColumn::make('name')->sortable(),
        ]);

        // Attach context to model
        TableContext::setForQuery($this->query, $context);

        $pipe = new SortPipe;
        $pipe->handle($this->query, fn ($q) => $q);

        $this->assertStringContainsString('order by "users"."name" desc', $this->query->toSql());
    }

    public function test_sort_pipe_falls_back_to_default_if_column_unsortable()
    {
        $request = new Request(['sort' => 'email', 'direction' => 'asc']);
        $config = new TableConfig; // defaultSort is 'id'
        $context = new TableContext($this->query, $request, $config, [
            TextColumn::make('email'), // Not sortable
        ]);

        TableContext::setForQuery($this->query, $context);

        $pipe = new SortPipe;
        $pipe->handle($this->query, fn ($q) => $q);

        // Should fall back to default sort 'id'
        $this->assertStringContainsString('order by "users"."id" desc', $this->query->toSql());
    }

    public function test_sort_pipe_does_not_sort_if_no_default()
    {
        $request = new Request(['sort' => 'email', 'direction' => 'asc']);
        $config = new TableConfig(defaultSort: ''); // No default sort
        $context = new TableContext($this->query, $request, $config, [
            TextColumn::make('email'), // Not sortable
        ]);

        TableContext::setForQuery($this->query, $context);

        $pipe = new SortPipe;
        $pipe->handle($this->query, fn ($q) => $q);

        $this->assertStringNotContainsString('order by', $this->query->toSql());
    }

    public function test_sort_pipe_applies_relation_sort()
    {
        // Category relation is defined in TestUser
        $request = new Request(['sort' => 'category_name', 'direction' => 'asc']);
        $config = new TableConfig;
        $context = new TableContext($this->query, $request, $config, [
            TextColumn::make('category.name')->sortable(),
        ]);

        TableContext::setForQuery($this->query, $context);

        $pipe = new SortPipe;
        $pipe->handle($this->query, fn ($q) => $q);

        $sql = $this->query->toSql();
        $this->assertStringContainsString('left join "categories"', $sql);
        $this->assertStringContainsString('order by "categories"."name" asc', $sql);
    }
}
