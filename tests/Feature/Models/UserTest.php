<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('User Model', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    describe('Basic Attributes', function () {
        it('has the correct fillable attributes', function () {
            $user = new User;
            $expectedFillable = [
                'name',
                'email',
                'password',
            ];

            expect($user->getFillable())->toBe($expectedFillable);
        });

        it('has the correct hidden attributes', function () {
            $user = new User;
            $expectedHidden = [
                'password',
                'remember_token',
            ];

            expect($user->getHidden())->toBe($expectedHidden);
        });

        it('has the correct casts', function () {
            $user = new User;
            $casts = $user->getCasts();

            expect($casts)->toHaveKey('email_verified_at');
            expect($casts['email_verified_at'])->toBe('datetime');
            expect($casts)->toHaveKey('password');
            expect($casts['password'])->toBe('hashed');
        });
    });

    describe('Relationships', function () {
        it('has many posts', function () {
            $posts = Post::factory()->count(3)->create(['user_id' => $this->user->id]);

            expect($this->user->posts)->toHaveCount(3);
            expect($this->user->posts->first())->toBeInstanceOf(Post::class);
        });

        it('posts relationship returns correct posts', function () {
            $userPosts = Post::factory()->count(2)->create(['user_id' => $this->user->id]);
            $otherUserPosts = Post::factory()->count(2)->create();

            expect($this->user->posts)->toHaveCount(2);
            expect($this->user->posts->pluck('id')->toArray())->toBe($userPosts->pluck('id')->toArray());
        });
    });

    describe('Model Methods', function () {
        it('generates correct initials', function () {
            $user = User::factory()->create(['name' => 'John Doe']);
            expect($user->initials())->toBe('JD');

            $user = User::factory()->create(['name' => 'Jane Smith Johnson']);
            expect($user->initials())->toBe('JS');

            $user = User::factory()->create(['name' => 'Alice']);
            expect($user->initials())->toBe('A');
        });

        it('can access filament panel', function () {
            $panel = \Mockery::mock(\Filament\Panel::class);
            expect($this->user->canAccessPanel($panel))->toBeTrue();
        });
    });

    describe('Model Configuration', function () {
        it('uses HasFactory trait', function () {
            $user = new User;
            expect(in_array('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses($user)))->toBeTrue();
        });

        it('uses Notifiable trait', function () {
            $user = new User;
            expect(in_array('Illuminate\Notifications\Notifiable', class_uses($user)))->toBeTrue();
        });

        it('uses TwoFactorAuthenticatable trait', function () {
            $user = new User;
            expect(in_array('Laravel\Fortify\TwoFactorAuthenticatable', class_uses($user)))->toBeTrue();
        });

        it('implements FilamentUser interface', function () {
            $user = new User;
            expect($user)->toBeInstanceOf(\Filament\Models\Contracts\FilamentUser::class);
        });

        it('extends Authenticatable class', function () {
            $user = new User;
            expect($user)->toBeInstanceOf(\Illuminate\Foundation\Auth\User::class);
        });
    });
});
