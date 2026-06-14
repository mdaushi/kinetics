<?php

namespace Kinetics\Tests\Unit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Kinetics\Filters\TextFilter;
use Kinetics\Pipes\FilterPipe;
use Kinetics\Support\TableConfig;
use Kinetics\Support\TableContext;
use Kinetics\Tests\Feature\TestUser;
use Kinetics\Tests\TestCase;

class FilterPipeTest extends TestCase
{
    protected Builder $query;

    protected function setUp(): void
    {
        parent::setUp();

        $model = new TestUser;
        $this->query = $model->newModelQuery();
    }

    public function test_filter_pipe_passes_through_if_no_context()
    {
        $pipe = new FilterPipe;

        $pipe->handle($this->query, function ($q) {
            return $q;
        });

        // Query should be unmodified
        $this->assertStringNotContainsString('where', $this->query->toSql());
    }

    public function test_filter_pipe_applies_registered_filters()
    {
        $request = new Request(['filters' => ['name' => 'John']]);
        $config = new TableConfig;
        $context = new TableContext($this->query, $request, $config, [], [
            TextFilter::make('name'),
        ]);

        TableContext::setForQuery($this->query, $context);

        $pipe = new FilterPipe;
        $pipe->handle($this->query, fn ($q) => $q);

        $this->assertStringContainsString('where "users"."name" like ?', $this->query->toSql());
        $this->assertEquals(['%John%'], $this->query->getBindings());
        $this->assertEquals(['name' => 'John'], $context->getMeta('filters'));
    }

    public function test_filter_pipe_ignores_unregistered_filters()
    {
        $request = new Request(['filters' => ['email' => 'john@example.com']]);
        $config = new TableConfig;
        $context = new TableContext($this->query, $request, $config, [], [
            TextFilter::make('name'), // Only name is registered
        ]);

        TableContext::setForQuery($this->query, $context);

        $pipe = new FilterPipe;
        $pipe->handle($this->query, fn ($q) => $q);

        $this->assertStringNotContainsString('where "users"."email" like ?', $this->query->toSql());
    }
}
