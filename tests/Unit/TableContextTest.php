<?php

namespace Kinetics\Tests\Unit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Kinetics\Columns\TextColumn;
use Kinetics\Filters\TextFilter;
use Kinetics\Support\TableConfig;
use Kinetics\Support\TableContext;
use Kinetics\Tests\TestCase;

class TableContextTest extends TestCase
{
    protected Builder $query;

    protected function setUp(): void
    {
        parent::setUp();

        $model = new class extends Model {};
        $this->query = $model->newModelQuery();
    }

    public function test_get_columns_returns_columns()
    {
        $columns = [TextColumn::make('name'), TextColumn::make('email')];
        $context = new TableContext($this->query, new Request, new TableConfig, $columns);

        $this->assertEquals($columns, $context->getColumns());
    }

    public function test_get_sortable_keys()
    {
        $columns = [
            TextColumn::make('name')->sortable(),
            TextColumn::make('email'),
        ];
        $context = new TableContext($this->query, new Request, new TableConfig, $columns);

        $this->assertEquals(['name'], $context->getSortableKeys());
    }

    public function test_get_searchable_keys()
    {
        $columns = [
            TextColumn::make('name')->searchable(),
            TextColumn::make('email'),
        ];
        $context = new TableContext($this->query, new Request, new TableConfig, $columns);

        $this->assertEquals(['name'], $context->getSearchableKeys());
    }

    public function test_get_filterable_keys()
    {
        $filters = [TextFilter::make('name')];
        $context = new TableContext($this->query, new Request, new TableConfig, [], $filters);

        $this->assertEquals(['name'], $context->getFilterableKeys());
    }

    public function test_get_relation_names()
    {
        $columns = [
            TextColumn::make('user.name'),
            TextColumn::make('user.email'),
            TextColumn::make('category.title'),
            TextColumn::make('local_col'),
        ];
        $context = new TableContext($this->query, new Request, new TableConfig, $columns);

        $this->assertEquals(['user', 'category'], $context->getRelationNames());
    }

    public function test_get_sort_column_and_direction()
    {
        $request = new Request(['sort' => 'name', 'direction' => 'desc']);
        $context = new TableContext($this->query, $request, new TableConfig, []);

        $this->assertEquals('name', $context->getSortColumn());
        $this->assertEquals('desc', $context->getSortDirection());
    }

    public function test_get_search()
    {
        $request = new Request(['search' => 'john']);
        $context = new TableContext($this->query, $request, new TableConfig, []);

        $this->assertEquals('john', $context->getSearch());
    }

    public function test_get_search_requires_min_length()
    {
        $request = new Request(['search' => 'j']);
        $context = new TableContext($this->query, $request, new TableConfig, []);

        $this->assertNull($context->getSearch());
    }

    public function test_get_per_page()
    {
        $request = new Request(['per_page' => 50]);
        $context = new TableContext($this->query, $request, new TableConfig, []);

        $this->assertEquals(50, $context->getPerPage());
    }

    public function test_get_per_page_uses_default()
    {
        $request = new Request;
        $config = new TableConfig(defaultPerPage: 20);
        $context = new TableContext($this->query, $request, $config, []);

        $this->assertEquals(20, $context->getPerPage());
    }

    public function test_get_per_page_capped_by_max()
    {
        $request = new Request(['per_page' => 500]);
        $config = new TableConfig(maxPerPage: 100);
        $context = new TableContext($this->query, $request, $config, []);

        $this->assertEquals(100, $context->getPerPage());
    }

    public function test_meta_bag()
    {
        $context = new TableContext($this->query, new Request, new TableConfig, []);

        $context->setMeta('key', 'value');

        $this->assertEquals('value', $context->getMeta('key'));
        $this->assertEquals('default', $context->getMeta('non_existent', 'default'));
        $this->assertEquals(['key' => 'value'], $context->allMeta());
    }
}
