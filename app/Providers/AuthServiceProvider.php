<?php

namespace App\Providers;

use App\Post;
use App\User;
use App\Topic;
use App\Comment;
use App\Section;
use App\Policies\UserPolicy;
use App\Policies\PostPolicy;
use App\Policies\TopicPolicy;
use App\Policies\CommentPolicy;
use App\Policies\SectionPolicy;
use Illuminate\Support\Facades\Gate;
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
        User::class     => UserPolicy::class,
        Post::class     => PostPolicy::class,
        Topic::class    => TopicPolicy::class,
        Section::class  => SectionPolicy::class,
        Comment::class  => CommentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
