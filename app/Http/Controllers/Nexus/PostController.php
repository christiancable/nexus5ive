<?php

namespace App\Http\Controllers\Nexus;

use App\Helpers\MentionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Nexus\StorePost;
use App\Http\Requests\Nexus\UpdatePost;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PostController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     */
    public function store(StorePost $request)
    {
        $topic = Topic::findOrFail($request->validated()['topic_id']);
        // todo - how do I know this is using the right policy here when I am passing the topic?
        if ($request->user()->cannot('create', $topic)) {
            abort(403);
        }

        $post = new Post($request->validated());
        $post->user_id = $request->user()->id;
        $post->popname = $request->user()->popname;
        $post->time = Carbon::now();
        $post->save();

        $request->user()->incrementTotalPosts();

        // scan post for mentions
        MentionHelper::makeMentions($post);

        // if we are viewing the topic with the most recent post at the bottom then
        // redirect to that point in the page
        if ($request->user()->viewLatestPostFirst) {
            $redirect = action('Nexus\TopicController@show', ['topic' => $post->topic_id]);
        } else {
            $redirect = action('Nexus\TopicController@show', ['topic' => $post->topic_id]).'#'.$post->id;
        }

        return redirect($redirect);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return RedirectResponse
     */
    public function update(UpdatePost $request, Post $post)
    {
        if ($request->user()->cannot('update', $post)) {
            abort(403);
        }

        $input = $request->validated();
        $updatedPost = $input['form']["$post->id"];
        $updatedPost['update_user_id'] = $request->user()->id;

        $post->update($updatedPost);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return RedirectResponse
     */
    public function destroy(Request $request, Post $post)
    {
        $this->authorize('delete', $post);

        // using forceDelete here because in this case we do not want a soft delete
        $post->forceDelete();

        return redirect()->route('topic.show', ['topic' => $post->topic_id]);
    }
}
