<?php
namespace App\ViewModels;

use Exception;

class UserPresenter
{
    /**
     * The authenticated user.
     *
     * @var User
     */
    protected $user;
    
    /**
     * Create a new Presenter instance.
     *
     * @param User $user
     */
    public function __construct(\App\User $user)
    {
        $this->user = $user;
    }
    /**
     * Handle dynamic property calls.
     *
     * @param  string $property
     * @return mixed
     */
    public function __get($property)
    {
        if (method_exists($this, $property)) {
            return call_user_func([$this, $property]);
        }
        $message = '%s does not respond to the "%s" property or method.';
        throw new Exception(
            sprintf($message, static::class, $property)
        );
    }

    public function profileLink()
    {
        $url = action('Nexus\UserController@show', ['username' => $this->user->username]);
        $html = <<< HTML
<span class="text-muted">@</span><mark><strong><a href="$url">{$this->user->username}</a></strong></mark>
HTML;
        return $html;
    }
}
