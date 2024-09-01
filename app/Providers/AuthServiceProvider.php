<?php

namespace App\Providers;

use App\Comment;
use App\Mode;
use App\Policies\CommentPolicy;
use App\Policies\ModePolicy;
use App\Policies\PostPolicy;
use App\Policies\SectionPolicy;
use App\Policies\TopicPolicy;
use App\Policies\UserPolicy;
use App\Post;
use App\Section;
use App\Topic;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model'     => 'App\Policies\ModelPolicy',
        User::class => UserPolicy::class,
        Post::class => PostPolicy::class,
        Topic::class => TopicPolicy::class,
        Section::class => SectionPolicy::class,
        Comment::class => CommentPolicy::class,
        Mode::class => ModePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot() {}
}
