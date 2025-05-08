<?php

namespace App\Http\Controllers\Nexus;

use App\Http\Controllers\Controller;
use App\Http\Requests\Nexus\UpdatePost;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
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
        if ($request->user()->cannot('delete', $post)) {
            abort(403);
        }

        // using forceDelete here because in this case we do not want a soft delete
        $post->forceDelete();

        return redirect()->route('topic.show', ['topic' => $post->topic_id]);
    }

    /**
     * flag a post for moderation
     */
    public function report(Request $request, Post $post)
    {
        // for annoy topics can the reporter see secrets?
        $userCanSeeSecrets = $request->user()->can('viewSecrets', $post->topic);

        return view(
            'nexus.moderation.report.post',
            [
                'post' => $post,
                'userCanSeeSecrets' => $userCanSeeSecrets,
            ],
        );
    }
}
