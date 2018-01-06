<?php

namespace App\Providers;

use App\Post;
use App\Comment;
use App\Section;
use App\Policies\PostPolicy;
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
        'App\Model'     => 'App\Policies\ModelPolicy',
        Section::class  => SectionPolicy::class,
        Comment::class  => CommentPolicy::class,
        Post::class     => PostPolicy::class,
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
