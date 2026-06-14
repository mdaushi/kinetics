<?php

namespace Kinetics\Tests\Unit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Kinetics\Columns\TextColumn;
use Kinetics\Pipes\SearchPipe;
use Kinetics\Support\TableConfig;
use Kinetics\Support\TableContext;
use Kinetics\Tests\Feature\TestUser;
use Kinetics\Tests\TestCase;

class SearchPipeTest extends TestCase
{
    protected Builder $query;

    protected function setUp(): void
    {
        parent::setUp();

        $model = new TestUser;
        $this->query = $model->newModelQuery();
    }

    public function test_search_pipe_passes_through_if_no_context_or_search()
    {
        $pipe = new SearchPipe;

        $pipe->handle($this->query, function ($q) {
            return $q;
        });

        // Query should be unmodified
        $this->assertStringNotContainsString('like', $this->query->toSql());
    }

    public function test_search_pipe_applies_local_column_search()
    {
        $request = new Request(['search' => 'john']);
        $config = new TableConfig;
        $context = new TableContext($this->query, $request, $config, [
            TextColumn::make('name')->searchable(),
            TextColumn::make('email'), // Not searchable
        ]);

        TableContext::setForQuery($this->query, $context);

        $pipe = new SearchPipe;
        $pipe->handle($this->query, fn ($q) => $q);

        $sql = $this->query->toSql();
        $this->assertStringContainsString('where ("users"."name" LIKE ?)', $sql);
        $this->assertEquals(['%john%'], $this->query->getBindings());
        $this->assertEquals('john', $context->getMeta('search'));
    }

    public function test_search_pipe_applies_relation_search()
    {
        $request = new Request(['search' => 'engineering']);
        $config = new TableConfig;
        $context = new TableContext($this->query, $request, $config, [
            TextColumn::make('category.name')->searchable(),
        ]);

        TableContext::setForQuery($this->query, $context);

        $pipe = new SearchPipe;
        $pipe->handle($this->query, fn ($q) => $q);

        $sql = $this->query->toSql();

        $this->assertStringContainsString('left join "categories"', $sql);
        $this->assertStringContainsString('"categories"."name" LIKE ?', $sql);
        $this->assertEquals(['%engineering%'], $this->query->getBindings());
    }
}
