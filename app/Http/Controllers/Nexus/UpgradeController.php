<?php

namespace Nexus\Http\Controllers\Nexus;

use Illuminate\Http\Request;

use Nexus\Http\Requests;
use Nexus\Http\Controllers\Controller;

class UpgradeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        // show all users in usertable
        
        $classicUsers = \DB::table('usertable')->get();
        
        foreach ($classicUsers as $classicUser) {
            $newUser = new \Nexus\User;
            
            $newUser->id = $classicUser->user_id;
            $newUser->username = $classicUser->user_name;
            $newUser->popname = $classicUser->user_popname;
            $newUser->about = $classicUser->user_comment;
            $newUser->location = $classicUser->user_town;
            
            if ($classicUser->user_sysop === 'y') {
                $newUser->administrator = true;
            } else {
                $newUser->administrator = false;
            }
            
            $newUser->totalVisits = $classicUser->user_totalvisits;
            $newUser->totalPosts = $classicUser->user_totaledits;
            $newUser->favouriteMovie = $classicUser->user_film;
            $newUser->favouriteMusic = $classicUser->user_band;
            
            if ($classicUser->user_hideemail === 'no') {
                $newUser->private = false;
            } else {
                $newUser->private = true;
            }
            
            $newUser->ipaddress = $classicUser->user_ipaddress;
            $lastLogin = \DB::table('whoison')->select('timeon')->where('user_id', $classicUser->user_id)->first();
            if ($lastLogin) {
                $newUser->latestLogin = $lastLogin->timeon;
            }
            
            if ($classicUser->user_status === 'Invalid') {
                $newUser->banned = true;
            }
            
            // avoid reusing email addresses
            $emailUses = \DB::table('usertable')->select('user_id')->where('user_email', $classicUser->user_email)->get();
            $count = count($emailUses);
            if ($count > 1) {
                $newUser->email = $classicUser->user_name . '@fakeemail.com';
            } else {
                $newUser->email = $classicUser->user_email;
            }

            $newUser->password = \Hash::make($classicUser->user_password);
            /*
            note sure about these....       
            $table->boolean('banned')->default(false);
        
            */
            
            if ($classicUser->user_realname != "") {
                $newUser->name = $classicUser->user_realname;
            } else {
                $newUser->name = "Unknown";
            }
            
            $newUser->save();
        }
        
        /* comments */

        $classicComments = \DB::table('commenttable')->get();
        
        foreach ($classicComments as $classicComment) {
            if (property_exists($classicComment, 'comment_id')) {
                try {
                    $newComment = new \Nexus\Comment;
                    \Log::info(get_object_vars($classicComment));
                    \Log::info('Comments: transferring ' . $classicComment->comment_id);
                    $newComment->id = $classicComment->comment_id;
                    $newComment->text = $classicComment->text;
                    $newComment->user_id = $classicComment->user_id;
                    $newComment->author_id = $classicComment->from_id;
                    
                    if ($classicComment->readstatus === 'n') {
                        $newComment->read = false;
                    } else {
                        $newComment->read = true;
                    }
                
                    $newComment->save();
                    
                } catch (\Exception $e) {
                    \Log::error('Comments: failed on ' . $classicComment->comment_id);
                }
            }
        }

        /* sections */

        $classicSections = \DB::table('sectiontable')->get();
    
        foreach ($classicSections as $classicSection) {
            try {
                $newSection = new \Nexus\Section;
                \Log::info('Section: transferring ' . $classicSection->section_id);

                $newSection->id = $classicSection->section_id;
                $newSection->title = $classicSection->section_title;
                $newSection->intro = $classicSection->section_intro;
                $newSection->user_id = $classicSection->user_id;
                $newSection->weight = $classicSection->section_weight;
                
                $newSection->save();

            } catch (\Exception $e) {
                \Log::error('Sections: failed on ' . $classicSection->section_id . $e);
            }
            
        }

        // then loop through the sections again and add in the parent relationships
        foreach ($classicSections as $classicSection) {
            try {
                $newSection = \Nexus\Section::findOrFail($classicSection->section_id);
                \Log::info('Section: adding parent to  ' . $classicSection->section_id);
                $newSection->parent_id = $classicSection->parent_id;
                $newSection->save();

            } catch (\Exception $e) {
                \Log::error('Sections: failed on ' . $classicSection->section_id . $e);
            }
            
        }

        $classicTopics = \DB::table('topictable')->get();

        foreach ($classicTopics as $classicTopic) {
            try {
                $newTopic = new \Nexus\Topic;
                \Log::info('Topic: transferring  ' . $classicTopic->topic_id);

                $newTopic->id = $classicTopic->topic_id;
                $newTopic->title = $classicTopic->topic_title;
                $newTopic->intro = $classicTopic->topic_description;
                $newTopic->section_id = $classicTopic->section_id;
                $newTopic->weight = $classicTopic->topic_weight;

                if ($classicTopic->topic_readonly === 'n') {
                    $newTopic->readonly = false;
                } else {
                    $newTopic->readonly = true;
                }

                if ($classicTopic->topic_annon === 'n') {
                    $newTopic->secret = false;
                } else {
                    $newTopic->secret = true;
                }
                
                $newTopic->save();

            } catch (\Exception $e) {
                \Log::error('Topic: failed on ' . $classicTopic->topic_id . $e);
            }

        }


        return view('upgrade.index', ['classicUsers' => $classicUsers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
