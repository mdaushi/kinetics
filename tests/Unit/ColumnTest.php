<?php

namespace Kinetics\Tests\Unit;

use Kinetics\Columns\TextColumn;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    // make() & defaults

    public function test_make_sets_key_and_defaults(): void
    {
        $col = TextColumn::make('name');

        $this->assertEquals('name', $col->getKey());
        $this->assertEquals('name', $col->getSourceKey());
        $this->assertFalse($col->isSortable());
        $this->assertFalse($col->isSearchable());
        $this->assertFalse($col->isFilterable());
        $this->assertNull($col->getRelation());
        $this->assertNull($col->getRelationKey());
        $this->assertNull($col->getFormatter());
    }

    public function test_label_is_derived_from_key(): void
    {
        $col = TextColumn::make('first_name');
        $arr = $col->toArray();

        $this->assertEquals('First Name', $arr['label']);
    }

    // Dot-notation

    public function test_dot_notation_sets_relation_and_output_key(): void
    {
        $col = TextColumn::make('user.name');

        $this->assertEquals('user', $col->getRelation());
        $this->assertEquals('name', $col->getRelationKey());
        $this->assertEquals('user_name', $col->getKey()); // outputKey
    }

    public function test_dot_notation_deep_path_sets_relation_key_with_dot(): void
    {
        // 'user.address.city' → relation='user', relationKey='address.city'
        $col = TextColumn::make('user.address.city');

        $this->assertEquals('user', $col->getRelation());
        $this->assertEquals('address.city', $col->getRelationKey());
        $this->assertEquals('user_address_city', $col->getKey());
    }

    public function test_dot_notation_auto_derives_label(): void
    {
        $col = TextColumn::make('category.name');
        $arr = $col->toArray();

        $this->assertEquals('Category Name', $arr['label']);
    }

    public function test_dot_notation_source_key_returns_output_key(): void
    {
        // For relation columns, getSourceKey() must return outputKey
        // so that formatUsing() can find the value after injection
        $col = TextColumn::make('user.name');

        $this->assertEquals('user_name', $col->getSourceKey());
    }

    // Explicit ->relation() override

    public function test_explicit_relation_overrides_dot_notation(): void
    {
        $col = TextColumn::make('author')
            ->relation('user', 'full_name');

        $this->assertEquals('user', $col->getRelation());
        $this->assertEquals('full_name', $col->getRelationKey());
        $this->assertEquals('author', $col->getKey());
    }

    public function test_explicit_relation_source_key_is_output_key(): void
    {
        $col = TextColumn::make('author_label')
            ->relation('user', 'name');

        $this->assertEquals('author_label', $col->getSourceKey());
    }

    // Fluent modifiers

    public function test_sortable_flag(): void
    {
        $col = TextColumn::make('name')->sortable();
        $this->assertTrue($col->isSortable());

        $col2 = TextColumn::make('name')->sortable(false);
        $this->assertFalse($col2->isSortable());
    }

    public function test_searchable_flag(): void
    {
        $col = TextColumn::make('name')->searchable();
        $this->assertTrue($col->isSearchable());
    }

    public function test_filterable_flag_and_options(): void
    {
        $col = TextColumn::make('status')->filterable(['active', 'inactive']);
        $arr = $col->toArray();

        $this->assertTrue($col->isFilterable());
        $this->assertEquals(['active', 'inactive'], $arr['filterOptions']);
    }

    public function test_hidden_column_visible_false(): void
    {
        $col = TextColumn::make('secret')->hidden();
        $arr = $col->toArray();

        $this->assertFalse($arr['visible']);
    }

    public function test_as_overrides_output_key(): void
    {
        $col = TextColumn::make('created_at')->as('created_formatted');

        $this->assertEquals('created_formatted', $col->getKey());
        $this->assertEquals('created_at', $col->getSourceKey());
    }

    public function test_label_override(): void
    {
        $col = TextColumn::make('name')->label('Full Name');
        $arr = $col->toArray();

        $this->assertEquals('Full Name', $arr['label']);
    }

    public function test_meta_single_key(): void
    {
        $col = TextColumn::make('status')->meta('color', 'red');
        $arr = $col->toArray();

        $this->assertEquals('red', $arr['meta']['color']);
    }

    public function test_meta_array_merge(): void
    {
        $col = TextColumn::make('status')
            ->meta('color', 'red')
            ->meta(['icon' => 'circle', 'size' => 'sm']);

        $arr = $col->toArray();

        $this->assertEquals('red', $arr['meta']['color']);
        $this->assertEquals('circle', $arr['meta']['icon']);
        $this->assertEquals('sm', $arr['meta']['size']);
    }

    public function test_format_using_stored(): void
    {
        $formatter = fn ($v) => strtoupper($v);
        $col = TextColumn::make('name')->formatUsing($formatter);

        $this->assertSame($formatter, $col->getFormatter());
    }

    // TextColumn specific

    public function test_badge_sets_type(): void
    {
        $col = TextColumn::make('status')->badge();
        $arr = $col->toArray();

        $this->assertEquals('badge', $arr['type']);
    }

    public function test_badge_with_false_condition_keeps_text_type(): void
    {
        $col = TextColumn::make('status')->badge(false);
        $arr = $col->toArray();

        $this->assertEquals('text', $arr['type']);
    }

    public function test_color_requires_badge_first(): void
    {
        $this->expectException(\LogicException::class);

        TextColumn::make('status')->color('default');
    }

    public function test_color_stored_in_meta(): void
    {
        $col = TextColumn::make('status')->badge()->color('destructive');
        $arr = $col->toArray();

        $this->assertEquals('destructive', $arr['meta']['color']);
    }

    public function test_color_map_stored_in_meta(): void
    {
        $map = ['active' => 'default', 'inactive' => 'secondary'];
        $col = TextColumn::make('status')->badge()->color($map);
        $arr = $col->toArray();

        $this->assertEquals($map, $arr['meta']['color']);
    }

    public function test_date_sets_formatter(): void
    {
        $col = TextColumn::make('created_at')->date('d/m/Y');

        $formatter = $col->getFormatter();
        $this->assertNotNull($formatter);
        $this->assertEquals('05/06/2024', $formatter('2024-06-05', [], null));
    }

    public function test_date_empty_value_returns_as_is(): void
    {
        $col = TextColumn::make('created_at')->date('d/m/Y');
        $formatter = $col->getFormatter();

        $this->assertEquals('', $formatter('', [], null));
        $this->assertNull($formatter(null, [], null));
    }

    public function test_datetime_format(): void
    {
        $col = TextColumn::make('created_at')->dateTime('Y-m-d H:i');
        $formatter = $col->getFormatter();

        $this->assertEquals('2024-06-05 09:30', $formatter('2024-06-05 09:30:00', [], null));
    }

    public function test_time_format(): void
    {
        $col = TextColumn::make('login_at')->time('H:i');
        $formatter = $col->getFormatter();

        $this->assertEquals('09:30', $formatter('2024-06-05 09:30:00', [], null));
    }

    // toArray shape

    public function test_to_array_contains_all_expected_keys(): void
    {
        $arr = TextColumn::make('name')->toArray();

        $this->assertArrayHasKey('key', $arr);
        $this->assertArrayHasKey('label', $arr);
        $this->assertArrayHasKey('sortable', $arr);
        $this->assertArrayHasKey('searchable', $arr);
        $this->assertArrayHasKey('filterable', $arr);
        $this->assertArrayHasKey('filterOptions', $arr);
        $this->assertArrayHasKey('visible', $arr);
        $this->assertArrayHasKey('type', $arr);
        $this->assertArrayHasKey('meta', $arr);
    }
}
