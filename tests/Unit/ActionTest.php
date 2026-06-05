<?php

namespace Kinetics\Tests\Unit;

use Kinetics\Actions\Action;
use Kinetics\Actions\ActionGroup;
use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    // Action::make()

    public function test_make_creates_action_with_key(): void
    {
        $action = Action::make('approve');
        $resolved = $action->label('Approve')->resolve(['id' => 1, 'status' => 'pending']);

        $this->assertEquals('approve', $resolved['key']);
        $this->assertEquals('Approve', $resolved['label']);
    }

    // Preset constructors

    public function test_edit_preset_has_correct_defaults(): void
    {
        $action = Action::edit();
        $resolved = $action->resolve(['id' => 1]);

        $this->assertEquals('edit', $resolved['key']);
        $this->assertEquals('Edit', $resolved['label']);
        $this->assertEquals('pencil', $resolved['icon']);
        $this->assertEquals('outline', $resolved['variant']);
    }

    public function test_delete_preset_has_confirmation(): void
    {
        $action = Action::delete();
        $resolved = $action->resolve(['id' => 1]);

        $this->assertEquals('delete', $resolved['key']);
        $this->assertEquals('destructive', $resolved['variant']);
        $this->assertNotNull($resolved['confirm']);
        $this->assertArrayHasKey('message', $resolved['confirm']);
        $this->assertArrayHasKey('title', $resolved['confirm']);
    }

    public function test_view_preset_has_correct_defaults(): void
    {
        $action = Action::view();
        $resolved = $action->resolve(['id' => 1]);

        $this->assertEquals('view', $resolved['key']);
        $this->assertEquals('eye',  $resolved['icon']);
    }

    // href resolution

    public function test_href_closure_resolved_per_row(): void
    {
        $action = Action::make('edit')
            ->label('Edit')
            ->href(fn($row) => '/users/' . $row['id'] . '/edit');

        $resolved = $action->resolve(['id' => 42]);

        $this->assertEquals('/users/42/edit', $resolved['href']);
    }

    public function test_href_null_when_not_set(): void
    {
        $action = Action::make('custom')->label('Custom');
        $resolved = $action->resolve(['id' => 1]);

        $this->assertNull($resolved['href']);
    }

    // visibleWhen

    public function test_visible_when_hides_action_when_false(): void
    {
        $action = Action::make('approve')
            ->label('Approve')
            ->visibleWhen(fn($row) => $row['status'] === 'pending');

        // Status 'active' → not visible → empty array
        $resolved = $action->resolve(['id' => 1, 'status' => 'active']);
        $this->assertEmpty($resolved);
    }

    public function test_visible_when_shows_action_when_true(): void
    {
        $action = Action::make('approve')
            ->label('Approve')
            ->visibleWhen(fn($row) => $row['status'] === 'pending');

        $resolved = $action->resolve(['id' => 1, 'status' => 'pending']);
        $this->assertEquals('approve', $resolved['key']);
    }

    // disabledWhen

    public function test_disabled_when_sets_disabled_flag(): void
    {
        $action = Action::make('edit')
            ->label('Edit')
            ->disabledWhen(fn($row) => $row['locked'] === true);

        $resolved = $action->resolve(['id' => 1, 'locked' => true]);
        $this->assertTrue($resolved['disabled']);

        $resolved2 = $action->resolve(['id' => 1, 'locked' => false]);
        $this->assertFalse($resolved2['disabled']);
    }

    // confirm

    public function test_confirm_sets_confirmation_payload(): void
    {
        $action = Action::make('delete')
            ->label('Delete')
            ->confirm('Delete record?', 'This cannot be undone.');

        $resolved = $action->resolve(['id' => 1]);

        $this->assertTrue($resolved['confirm'] !== null);
        $this->assertEquals('Delete record?',       $resolved['confirm']['title']);
        $this->assertEquals('This cannot be undone.', $resolved['confirm']['message']);
    }

    public function test_no_confirm_returns_null(): void
    {
        $action = Action::make('view')->label('View');
        $resolved = $action->resolve(['id' => 1]);

        $this->assertNull($resolved['confirm']);
    }

    // variant validation

    public function test_invalid_variant_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Action::make('test')->variant('invalid-variant');
    }

    public function test_valid_variants_accepted(): void
    {
        foreach (['default', 'destructive', 'ghost', 'outline'] as $variant) {
            $action = Action::make('test')->label('Test')->variant($variant);
            $resolved = $action->resolve(['id' => 1]);

            $this->assertEquals($variant, $resolved['variant']);
        }
    }

    // method

    public function test_method_is_uppercased(): void
    {
        $action = Action::make('remove')->label('Remove')->method('delete');
        $resolved = $action->resolve(['id' => 1]);

        $this->assertEquals('DELETE', $resolved['method']);
    }

    // modal

    public function test_modal_flag(): void
    {
        $action = Action::make('edit')->label('Edit')->modal();
        $resolved = $action->resolve(['id' => 1]);

        $this->assertTrue($resolved['modal']);
    }

    // meta

    public function test_meta_included_in_resolved(): void
    {
        $action = Action::make('custom')
            ->label('Custom')
            ->meta(['permissions' => ['admin'], 'confirm_type' => 'danger']);

        $resolved = $action->resolve(['id' => 1]);

        $this->assertEquals(['admin'],  $resolved['meta']['permissions']);
        $this->assertEquals('danger',   $resolved['meta']['confirm_type']);
    }

    // ActionGroup

    public function test_action_group_resolve_includes_visible_actions(): void
    {
        $group = ActionGroup::make('More')
            ->actions([
                Action::make('approve')->label('Approve')
                    ->visibleWhen(fn($r) => $r['status'] === 'pending'),
                Action::make('reject')->label('Reject')
                    ->visibleWhen(fn($r) => $r['status'] === 'pending'),
                Action::make('archive')->label('Archive'),
            ]);

        $resolved = $group->resolve(['id' => 1, 'status' => 'pending']);

        $this->assertEquals('group', $resolved['type']);
        $this->assertEquals('More',  $resolved['label']);
        $this->assertCount(3, $resolved['actions']); // semua visible
    }

    public function test_action_group_filters_invisible_actions(): void
    {
        $group = ActionGroup::make('More')
            ->actions([
                Action::make('approve')->label('Approve')
                    ->visibleWhen(fn($r) => $r['status'] === 'pending'),
                Action::make('archive')->label('Archive'), // selalu visible
            ]);

        $resolved = $group->resolve(['id' => 1, 'status' => 'active']);

        // approve tidak visible → hanya archive
        $this->assertCount(1, $resolved['actions']);
        $this->assertEquals('archive', $resolved['actions'][0]['key']);
    }

    public function test_action_group_default_icon(): void
    {
        $group = ActionGroup::make();
        $resolved = $group->resolve(['id' => 1]);

        $this->assertEquals('ellipsis-vertical', $resolved['icon']);
    }

    public function test_action_group_custom_icon(): void
    {
        $group = ActionGroup::make()->icon('dots-horizontal');
        $resolved = $group->resolve(['id' => 1]);

        $this->assertEquals('dots-horizontal', $resolved['icon']);
    }
}
