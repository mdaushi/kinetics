<?php

namespace Kinetics\Tests\Unit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Kinetics\Filters\Filter;
use Kinetics\Tests\TestCase;

class DummyFilter extends Filter
{
    protected ?string $type = 'dummy';

    protected array $operators = ['equals', 'contains'];

    public function apply(Builder $query, mixed $payload): void
    {
        [$operator, $value] = $this->parsePayload($payload);
        $this->applyOperator($query, $this->getColumn(), $operator, $value);
    }
}

class FilterTest extends TestCase
{
    public function test_make_sets_key_and_generates_label()
    {
        $filter = DummyFilter::make('user.first_name');

        $this->assertEquals('user.first_name', $filter->getKey());
        $toArray = $filter->toArray();
        $this->assertEquals('User First Name', $toArray['label']);
    }

    public function test_label_override()
    {
        $filter = DummyFilter::make('name')->label('Custom Label');
        $this->assertEquals('Custom Label', $filter->toArray()['label']);
    }

    public function test_column_override()
    {
        $filter = DummyFilter::make('name')->column('users.name');
        $this->assertEquals('users.name', $filter->getColumn());
    }

    public function test_default_column_is_key()
    {
        $filter = DummyFilter::make('name');
        $this->assertEquals('name', $filter->getColumn());
    }

    public function test_operators_override()
    {
        $filter = DummyFilter::make('name')->operators(['starts_with', 'ends_with' => 'Ends']);

        $array = $filter->toArray();

        $this->assertCount(2, $array['operators']);
        $this->assertEquals('starts_with', $array['operators'][0]['value']);
        $this->assertEquals('Starts With', $array['operators'][0]['label']);

        $this->assertEquals('ends_with', $array['operators'][1]['value']);
        $this->assertEquals('Ends', $array['operators'][1]['label']);
    }

    public function test_get_available_universal_operators()
    {
        $operators = Filter::getAvailableUniversalOperators();
        $this->assertContains('equals', $operators);
        $this->assertContains('contains', $operators);
        $this->assertContains('>', $operators);
    }

    public function test_resolve_custom_operator()
    {
        Filter::resolveOperator('custom_op', function ($query, $column, $value) {
            $query->whereRaw("$column = ? + 1", [$value]);
        });

        $operators = Filter::getAvailableUniversalOperators();
        $this->assertContains('custom_op', $operators);

        $filter = DummyFilter::make('age')->operators(['custom_op']);

        $model = new class extends Model
        {
            protected $table = 'users';
        };
        $query = $model->newModelQuery();
        $query->setQuery(DB::table('users'));

        $filter->apply($query, ['operator' => 'custom_op', 'value' => 10]);

        $this->assertEquals('select * from "users" where age = ? + 1', $query->toSql());
        $this->assertEquals([10], $query->getBindings());
    }
}
