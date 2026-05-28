<?php

namespace Kinetics\Tests\Feature;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Kinetics\Columns\Column;
use Kinetics\Exceptions\InvalidPipeException;
use Kinetics\Pipes\SearchPipe;
use Kinetics\Table;
use Kinetics\Tests\TestCase;

class TableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('role');
            $table->timestamps();
        });

        TestUser::create(['name' => 'fulan', 'email' => 'fulan@example.com', 'role' => 'admin']);
        TestUser::create(['name' => 'Ucok', 'email' => 'ucok@example.com', 'role' => 'admin']);
        TestUser::create(['name' => 'Om Bob', 'email' => 'bob@example.com', 'role' => 'user']);
    }

    // Output shape

    public function test_make_returns_correct_shape(): void
    {
        $result = Table::model(TestUser::class)
            ->columns([
                Column::make('name')->sortable()->searchable(),
                Column::make('email')->sortable(),
            ])
            ->withRequest(new Request())
            ->make();

        $this->assertArrayHasKey('data',    $result);
        $this->assertArrayHasKey('columns', $result);
        $this->assertArrayHasKey('meta',    $result);
        $this->assertArrayHasKey('state',   $result);
    }

    public function test_data_contains_all_records_by_default(): void
    {
        $result = Table::model(TestUser::class)
            ->columns([Column::make('name')])
            ->withRequest(new Request())
            ->make();

        $this->assertCount(3, $result['data']);
    }

    public function test_columns_shape_is_correct(): void
    {
        $result = Table::model(TestUser::class)
            ->columns([
                Column::make('name')->sortable()->searchable(),
            ])
            ->withRequest(new Request())
            ->make();

        $column = $result['columns'][0];

        $this->assertEquals('name', $column['key']);
        $this->assertTrue($column['sortable']);
        $this->assertTrue($column['searchable']);
        $this->assertFalse($column['filterable']);
    }

    // Sorting

    public function test_sorts_by_name_ascending(): void
    {
        $request = new Request(['sort' => 'name', 'direction' => 'asc']);

        $result = Table::model(TestUser::class)
            ->columns([Column::make('name')->sortable()])
            ->withRequest($request)
            ->make();

        $names = array_column($result['data'], 'name');
        $this->assertEquals(['Om Bob', 'Ucok', 'fulan'], $names);
    }

    public function test_sorts_by_name_descending(): void
    {
        $request = new Request(['sort' => 'name', 'direction' => 'desc']);

        $result = Table::model(TestUser::class)
            ->columns([Column::make('name')->sortable()])
            ->withRequest($request)
            ->make();

        $names = array_column($result['data'], 'name');
        $this->assertEquals(['fulan', 'Ucok', 'Om Bob'], $names);
    }

    public function test_ignores_sort_on_non_sortable_column(): void
    {
        // 'email' tidak didaftarkan sebagai sortable — harus diabaikan
        $request = new Request(['sort' => 'email', 'direction' => 'asc']);

        $result = Table::model(TestUser::class)
            ->columns([
                Column::make('name')->sortable(),
                Column::make('email'), // tidak sortable
            ])
            ->withRequest($request)
            ->make();

        // Tidak throw exception — query tetap jalan dengan default sort
        $this->assertCount(3, $result['data']);
    }

    // Search

    public function test_search_filters_by_searchable_columns(): void
    {
        $request = new Request(['search' => 'fulan']);

        $result = Table::model(TestUser::class)
            ->columns([
                Column::make('name')->searchable(),
                Column::make('email')->searchable(),
            ])
            ->withRequest($request)
            ->make();

        $this->assertCount(1, $result['data']);
        $this->assertEquals('fulan', $result['data'][0]['name']);
    }

    public function test_search_less_than_2_chars_is_ignored(): void
    {
        $request = new Request(['search' => 'a']); // 1 karakter

        $result = Table::model(TestUser::class)
            ->columns([Column::make('name')->searchable()])
            ->withRequest($request)
            ->make();

        $this->assertCount(3, $result['data']);
    }

    public function test_search_is_case_insensitive(): void
    {
        $request = new Request(['search' => 'FULAN']);

        $result = Table::model(TestUser::class)
            ->columns([Column::make('name')->searchable()])
            ->withRequest($request)
            ->make();

        $this->assertCount(1, $result['data']);
    }

    // Filter

    public function test_filter_by_exact_value(): void
    {
        $request = new Request(['filters' => ['role' => 'admin']]);

        $result = Table::model(TestUser::class)
            ->columns([Column::make('role')->filterable(['admin', 'user'])])
            ->withRequest($request)
            ->make();

        $this->assertCount(2, $result['data']);
        foreach ($result['data'] as $row) {
            $this->assertEquals('admin', $row['role']);
        }
    }

    public function test_filter_ignores_non_filterable_column(): void
    {
        // Attempt to filter 'name' which is NOT filterable
        $request = new Request(['filters' => ['name' => 'fulan']]);

        $result = Table::model(TestUser::class)
            ->columns([Column::make('name')]) // no ->filterable()
            ->withRequest($request)
            ->make();

        // Filter diabaikan — semua data tetap muncul
        $this->assertCount(3, $result['data']);
    }

    // Pagination

    public function test_pagination_meta_is_correct(): void
    {
        $result = Table::model(TestUser::class)
            ->columns([Column::make('name')])
            ->perPage(2)
            ->withRequest(new Request(['page' => 1]))
            ->make();

        $meta = $result['meta'];
        $this->assertEquals(1, $meta['current_page']);
        $this->assertEquals(2, $meta['last_page']);
        $this->assertEquals(2, $meta['per_page']);
        $this->assertEquals(3, $meta['total']);
    }

    public function test_per_page_is_capped_at_max(): void
    {
        $request = new Request(['per_page' => 999]);

        $result = Table::model(TestUser::class)
            ->columns([Column::make('name')])
            ->perPage(15, 50) // max 50
            ->withRequest($request)
            ->make();

        $this->assertEquals(50, $result['meta']['per_page']);
    }

    // Pipeline

    public function test_custom_pipe_is_applied(): void
    {
        $adminOnlyPipe = new class implements \Kinetics\Contracts\PipeInterface {
            public function handle(Builder $query, \Closure $next): mixed
            {
                $query->where('role', 'admin');
                return $next($query);
            }
        };

        $result = Table::model(TestUser::class)
            ->columns([Column::make('name')])
            ->pipes([$adminOnlyPipe])
            ->withRequest(new Request())
            ->make();

        $this->assertCount(2, $result['data']);
    }

    public function test_withoutPipes_removes_search(): void
    {
        $request = new Request(['search' => 'alice']);

        $result = Table::model(TestUser::class)
            ->columns([Column::make('name')->searchable()])
            ->withoutPipes([SearchPipe::class])
            ->withRequest($request)
            ->make();

        // SearchPipe dihapus, semua data tetap muncul meski ada search query
        $this->assertCount(3, $result['data']);
    }

    public function test_throws_on_invalid_pipe(): void
    {
        $this->expectException(InvalidPipeException::class);

        Table::model(TestUser::class)
            ->columns([Column::make('name')])
            ->pipes([\stdClass::class]); // bukan PipeInterface
    }

    // Column formatter

    public function test_column_formatter_transforms_value(): void
    {
        $result = Table::model(TestUser::class)
            ->columns([
                Column::make('role')
                    ->formatUsing(fn($val) => strtoupper($val)),
            ])
            ->withRequest(new Request())
            ->make();

        foreach ($result['data'] as $row) {
            $this->assertMatchesRegularExpression('/^[A-Z]+$/', $row['role']);
        }
    }

    // State sync

    public function test_state_reflects_current_request(): void
    {
        $request = new Request([
            'sort'      => 'name',
            'direction' => 'desc',
            'search'    => 'ali',
            'filters'   => ['role' => 'admin'],
        ]);

        $result = Table::model(TestUser::class)
            ->columns([
                Column::make('name')->sortable()->searchable(),
                Column::make('role')->filterable(),
            ])
            ->withRequest($request)
            ->make();

        $state = $result['state'];
        $this->assertEquals('name',  $state['sort']);
        $this->assertEquals('desc',  $state['direction']);
        $this->assertEquals('ali',   $state['search']);
        $this->assertEquals(['role' => 'admin'], $state['filters']);
    }
}

class TestUser extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'role'];
}
