<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

it('renders successfully', function () {
    Livewire::test(Register::class)
        ->assertStatus(200);
});

it('should be able to register a new user in the system', function () {
    Livewire::test(Register::class)
        ->set('name', 'Joe Doe')
        ->set('email', 'joe@doe.com')
        ->set('email_confirmation', 'joe@doe.com')
        ->set('password', 'password')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(RouteServiceProvider::HOME);

    assertDatabaseHas('users', [
        'name' => 'Joe Doe',
        'email' => 'joe@doe.com',
    ]);

    assertDatabaseCount('users', 1);

    expect(auth()->check())
        ->and(auth()->user())
        ->id->toBe(User::first()->id);
});

test('validation rules', function ($f) {
    if ($f->rule === 'unique') {
        User::factory()->create([$f->field => $f->value]);
    }
    $livewire = Livewire::test(Register::class)
        ->set($f->field, $f->value);

    if (property_exists($f, 'aValue')) {
        $livewire->set($f->aField, $f->aValue);
    }

    $livewire->call('submit')
        ->assertHasErrors([$f->field => $f->rule]);
})->with([
    'name::required' => (object) ['field' => 'name', 'value' => '', 'rule' => 'required'],
    'name::min' => (object) ['field' => 'name', 'value' => '**', 'rule' => 'min'],
    'name::max:255' => (object) ['field' => 'name', 'value' => str_repeat('*', 256), 'rule' => 'max'],

    'email::required' => (object) ['field' => 'email', 'value' => '', 'rule' => 'required'],
    'email::email' => (object) ['field' => 'email', 'value' => 'not-an-email', 'rule' => 'email'],
    'email::max:255' => (object) ['field' => 'email', 'value' => str_repeat('*'.'@doe.com', 256), 'rule' => 'max'],
    'email::confirmed' => (object) ['field' => 'email', 'value' => 'joe@joe.com', 'rule' => 'confirmed'],
    'email::unique' => (object) ['field' => 'email', 'value' => 'joe@joe.com', 'rule' => 'unique', 'aField' => 'email_confirmation', 'aValue' => 'joe@joe.com'],

    'password::required' => (object) ['field' => 'password', 'value' => '', 'rule' => 'required'],
]);

it('should send a notification welcoming the new user', function () {
    Notification::fake();

    Livewire::test(Register::class)
        ->set('name', 'Joe doe')
        ->set('email', 'joe@doe.com')
        ->set('email_confirmation', 'joe@doe.com')
        ->set('password', 'password')
        ->call('submit');

    $user = User::whereEmail('joe@doe.com')->first();

    Notification::assertSentTo($user, WelcomeNotification::class);
});
