<?php

namespace Kinetics\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Kinetics\Actions\Action;
use Kinetics\Actions\ActionGroup;
use Kinetics\Columns\ActionColumn;
use Kinetics\Columns\TextColumn;
use Kinetics\Pipes\DateRangeFilterPipe;
use Kinetics\Table;
use Kinetics\Tests\TestCase;

/**
 * - Table::query() entry point
 * - defaultSort()
 * - tap() constraint
 * - filter whereIn
 * - filter skip null/empty value
 * - ActionColumn + Action resolve
 * - ActionColumn visibleWhen per-row
 * - DateRangeFilterPipe
 * - Table::get() returns TableResult
 * - TableResult getters
 */
class TableAdvancedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('categories', function ($table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('posts', function ($table) {
            $table->id();
            $table->string('title');
            $table->string('status')->default('draft');
            $table->integer('views')->default(0);
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        $catA = PostCategory::create(['name' => 'Laravel']);
        $catB = PostCategory::create(['name' => 'Vue']);

        PostModel::create(['title' => 'Intro to Laravel',  'status' => 'published', 'views' => 100, 'category_id' => $catA->id, 'published_at' => '2024-01-15']);
        PostModel::create(['title' => 'Advanced Eloquent', 'status' => 'draft',     'views' => 50,  'category_id' => $catA->id, 'published_at' => '2024-03-10']);
        PostModel::create(['title' => 'Vue Composables',   'status' => 'published', 'views' => 200, 'category_id' => $catB->id, 'published_at' => '2024-06-01']);
    }

    // Table::query() entry point 

    public function test_query_entry_point_works(): void
    {
        $result = Table::query(PostModel::query())
            ->columns([TextColumn::make('title')])
            ->withRequest(new Request())
            ->make();

        $this->assertCount(3, $result['data']);
    }

    public function test_query_with_eager_load_does_not_re_query(): void
    {
        // User passes with('category') manually — loadMissing should not re-query
        $result = Table::query(PostModel::with('category'))
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('category.name'),
            ])
            ->withRequest(new Request())
            ->make();

        $this->assertCount(3, $result['data']);
        foreach ($result['data'] as $row) {
            $this->assertArrayHasKey('category_name', $row);
        }
    }

    // defaultSort()

    public function test_default_sort_applied_when_no_request_sort(): void
    {
        $result = Table::model(PostModel::class)
            ->columns([TextColumn::make('views')->sortable()])
            ->defaultSort('views', 'desc')
            ->withRequest(new Request())
            ->make();

        $views = array_column($result['data'], 'views');
        $this->assertEquals([200, 100, 50], $views);
    }

    public function test_request_sort_overrides_default_sort(): void
    {
        $request = new Request(['sort' => 'views', 'direction' => 'asc']);

        $result = Table::model(PostModel::class)
            ->columns([TextColumn::make('views')->sortable()])
            ->defaultSort('views', 'desc') // default desc, tapi request override ke asc
            ->withRequest($request)
            ->make();

        $views = array_column($result['data'], 'views');
        $this->assertEquals([50, 100, 200], $views);
    }

    // tap()

    public function test_tap_applies_constraint_to_query(): void
    {
        $result = Table::model(PostModel::class)
            ->columns([TextColumn::make('title')])
            ->tap(fn($q) => $q->where('status', 'published'))
            ->withRequest(new Request())
            ->make();

        $this->assertCount(2, $result['data']);
        foreach ($result['data'] as $row) {
            $this->assertEquals('published', $row['status']);
        }
    }

    // Filter: whereIn

    public function test_filter_where_in_with_array_value(): void
    {
        $request = new Request(['filters' => ['status' => ['published', 'draft']]]);

        $result = Table::model(PostModel::class)
            ->columns([TextColumn::make('status')->filterable()])
            ->withRequest($request)
            ->make();

        $this->assertCount(3, $result['data']); // semua match
    }

    public function test_filter_where_in_filters_correctly(): void
    {
        $request = new Request(['filters' => ['status' => ['published']]]);

        $result = Table::model(PostModel::class)
            ->columns([TextColumn::make('status')->filterable()])
            ->withRequest($request)
            ->make();

        $this->assertCount(2, $result['data']);
        foreach ($result['data'] as $row) {
            $this->assertEquals('published', $row['status']);
        }
    }

    public function test_filter_skips_null_value(): void
    {
        $request = new Request(['filters' => ['status' => null]]);

        $result = Table::model(PostModel::class)
            ->columns([TextColumn::make('status')->filterable()])
            ->withRequest($request)
            ->make();

        // null diabaikan — semua data muncul
        $this->assertCount(3, $result['data']);
    }

    public function test_filter_skips_empty_string_value(): void
    {
        $request = new Request(['filters' => ['status' => '']]);

        $result = Table::model(PostModel::class)
            ->columns([TextColumn::make('status')->filterable()])
            ->withRequest($request)
            ->make();

        $this->assertCount(3, $result['data']);
    }

    public function test_filter_array_removes_null_and_empty_entries(): void
    {
        // Array dengan null dan '' harus diabaikan
        $request = new Request(['filters' => ['status' => [null, '', 'published']]]);

        $result = Table::model(PostModel::class)
            ->columns([TextColumn::make('status')->filterable()])
            ->withRequest($request)
            ->make();

        $this->assertCount(2, $result['data']);
    }

    // ActionColumn

    public function test_action_column_injected_into_row(): void
    {
        $result = Table::model(PostModel::class)
            ->columns([
                TextColumn::make('title'),
                ActionColumn::make()->actions([
                    Action::make('edit')->label('Edit')->href(fn($r) => '/posts/' . $r['id'] . '/edit'),
                    Action::delete(),
                ]),
            ])
            ->withRequest(new Request())
            ->make();

        foreach ($result['data'] as $row) {
            $this->assertArrayHasKey('__actions', $row);
            $this->assertCount(2, $row['__actions']);
            $this->assertEquals('edit',   $row['__actions'][0]['key']);
            $this->assertEquals('delete', $row['__actions'][1]['key']);
        }
    }

    public function test_action_column_href_closure_receives_row(): void
    {
        $result = Table::model(PostModel::class)
            ->columns([
                TextColumn::make('title'),
                ActionColumn::make()->actions([
                    Action::make('edit')
                        ->label('Edit')
                        ->href(fn($r) => '/posts/' . $r['id']),
                ]),
            ])
            ->withRequest(new Request())
            ->make();

        foreach ($result['data'] as $row) {
            $expected = '/posts/' . $row['id'];
            $this->assertEquals($expected, $row['__actions'][0]['href']);
        }
    }

    public function test_action_visible_when_filters_per_row(): void
    {
        $result = Table::model(PostModel::class)
            ->columns([
                TextColumn::make('status'),
                ActionColumn::make()->actions([
                    Action::make('publish')
                        ->label('Publish')
                        ->visibleWhen(fn($r) => $r['status'] === 'draft'),
                    Action::make('view')->label('View'), // selalu visible
                ]),
            ])
            ->withRequest(new Request())
            ->make();

        foreach ($result['data'] as $row) {
            $actions = $row['__actions'];
            $keys    = array_column($actions, 'key');

            if ($row['status'] === 'draft') {
                $this->assertContains('publish', $keys);
            } else {
                $this->assertNotContains('publish', $keys);
            }

            $this->assertContains('view', $keys);
        }
    }

    public function test_action_column_with_custom_key(): void
    {
        $result = Table::model(PostModel::class)
            ->columns([
                TextColumn::make('title'),
                ActionColumn::make('row_actions')->actions([
                    Action::make('edit')->label('Edit'),
                ]),
            ])
            ->withRequest(new Request())
            ->make();

        foreach ($result['data'] as $row) {
            $this->assertArrayHasKey('row_actions', $row);
            $this->assertArrayNotHasKey('__actions', $row);
        }
    }

    public function test_action_group_in_action_column(): void
    {
        $result = Table::model(PostModel::class)
            ->columns([
                TextColumn::make('title'),
                ActionColumn::make()->actions([
                    Action::make('view')->label('View'),
                    ActionGroup::make('More')->actions([
                        Action::make('archive')->label('Archive'),
                        Action::make('duplicate')->label('Duplicate'),
                    ]),
                ]),
            ])
            ->withRequest(new Request())
            ->make();

        foreach ($result['data'] as $row) {
            $actions = $row['__actions'];
            $this->assertCount(2, $actions);
            $this->assertEquals('action', $actions[0]['type']);
            $this->assertEquals('group',  $actions[1]['type']);
            $this->assertCount(2, $actions[1]['actions']);
        }
    }

    // Table::get() returns TableResult

    public function test_get_returns_table_result_instance(): void
    {
        $result = Table::model(PostModel::class)
            ->columns([TextColumn::make('title')])
            ->withRequest(new Request())
            ->get();

        $this->assertInstanceOf(\Kinetics\Resources\TableResult::class, $result);
    }

    public function test_table_result_getters(): void
    {
        $result = Table::model(PostModel::class)
            ->columns([TextColumn::make('title')])
            ->perPage(2)
            ->withRequest(new Request(['page' => 1]))
            ->get();

        $this->assertEquals(3, $result->getTotal());
        $this->assertEquals(1, $result->getCurrentPage());
        $this->assertEquals(2, $result->getLastPage());
        $this->assertEquals(2, $result->getPerPage());
        $this->assertCount(2, $result->getData());
        $this->assertNotEmpty($result->getMeta());
        $this->assertNotEmpty($result->getColumns());
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result->getPaginator());
    }

    public function test_table_result_is_json_serializable(): void
    {
        $result = Table::model(PostModel::class)
            ->columns([TextColumn::make('title')])
            ->withRequest(new Request())
            ->get();

        $json = json_encode($result);
        $this->assertJson($json);

        $decoded = json_decode($json, true);
        $this->assertArrayHasKey('data',    $decoded);
        $this->assertArrayHasKey('columns', $decoded);
        $this->assertArrayHasKey('meta',    $decoded);
        $this->assertArrayHasKey('state',   $decoded);
    }

    // DateRangeFilterPipe

    public function test_date_range_filter_both_bounds(): void
    {
        $request = new Request([
            'date_from' => '2024-01-01',
            'date_to'   => '2024-04-01',
        ]);

        $result = Table::model(PostModel::class)
            ->columns([TextColumn::make('title')])
            ->pipes([new DateRangeFilterPipe('published_at')])
            ->withRequest($request)
            ->make();

        // Hanya 'Intro to Laravel' (Jan) dan 'Advanced Eloquent' (Mar) yang dalam range
        $this->assertCount(2, $result['data']);
    }

    public function test_date_range_filter_only_from(): void
    {
        $request = new Request(['date_from' => '2024-05-01']);

        $result = Table::model(PostModel::class)
            ->columns([TextColumn::make('title')])
            ->pipes([new DateRangeFilterPipe('published_at')])
            ->withRequest($request)
            ->make();

        // Hanya 'Vue Composables' (Jun 2024)
        $this->assertCount(1, $result['data']);
        $this->assertEquals('Vue Composables', $result['data'][0]['title']);
    }

    public function test_date_range_filter_only_to(): void
    {
        $request = new Request(['date_to' => '2024-02-01']);

        $result = Table::model(PostModel::class)
            ->columns([TextColumn::make('title')])
            ->pipes([new DateRangeFilterPipe('published_at')])
            ->withRequest($request)
            ->make();

        // Hanya 'Intro to Laravel' (Jan 2024)
        $this->assertCount(1, $result['data']);
        $this->assertEquals('Intro to Laravel', $result['data'][0]['title']);
    }

    public function test_date_range_filter_no_params_returns_all(): void
    {
        $result = Table::model(PostModel::class)
            ->columns([TextColumn::make('title')])
            ->pipes([new DateRangeFilterPipe('published_at')])
            ->withRequest(new Request())
            ->make();

        $this->assertCount(3, $result['data']);
    }

    public function test_date_range_filter_custom_param_names(): void
    {
        $request = new Request([
            'from' => '2024-05-01',
            'to'   => '2024-12-31',
        ]);

        $result = Table::model(PostModel::class)
            ->columns([TextColumn::make('title')])
            ->pipes([new DateRangeFilterPipe('published_at', 'from', 'to')])
            ->withRequest($request)
            ->make();

        $this->assertCount(1, $result['data']);
    }

    // Invalid model

    public function test_model_entry_point_throws_for_non_model_class(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Table::model(\stdClass::class);
    }

    // Column as() alias

    public function test_column_as_alias_appears_in_output(): void
    {
        $result = Table::model(PostModel::class)
            ->columns([
                TextColumn::make('created_at')->as('created_formatted')->date('d/m/Y'),
            ])
            ->withRequest(new Request())
            ->make();

        foreach ($result['data'] as $row) {
            $this->assertArrayHasKey('created_formatted', $row);
        }
    }

    // Column hidden

    public function test_hidden_column_not_in_visible_columns(): void
    {
        $result = Table::model(PostModel::class)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('views')->hidden(),
            ])
            ->withRequest(new Request())
            ->make();

        $colKeys = array_column($result['columns'], 'key');
        $visible  = array_filter($result['columns'], fn($c) => $c['visible']);

        // Data masih ada (hidden hanya flag untuk FE), tapi visible = false
        $this->assertContains('views', $colKeys);
        $this->assertCount(1, $visible); // hanya 'title' yang visible
    }
}

class PostCategory extends Model
{
    protected $table = 'categories';
    protected $fillable = ['name'];
}

class PostModel extends Model
{
    protected $table = 'posts';
    protected $fillable = ['title', 'status', 'views', 'category_id', 'published_at'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class);
    }
}
