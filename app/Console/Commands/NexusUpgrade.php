<?php

namespace Nexus\Console\Commands;

use Illuminate\Console\Command;

class NexusUpgrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexus:upgrade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates old Nexus5 data into Laravel Models';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
       //
        // show all users in usertable
        
        $this->info('Upgrade: Begin');

        $this->info('Upgrade: Users Start');

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

        unset($classicUsers);

        $this->info('Upgrade: Users Complete');

        $this->info('Upgrade: Comments Start');
        
        /* comments */

        $classicComments = \DB::table('commenttable')->get();
        
        foreach ($classicComments as $classicComment) {
            if (property_exists($classicComment, 'comment_id')) {
                try {
                    $newComment = new \Nexus\Comment;
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
                    $this->error('Upgrade failed on comment ' . $classicComment->comment_id . $e);
                }
            }
        }
        unset($classicComments);
        $this->info('Upgrade: Complete Complete');

        $this->info('Upgrade: Sections Start');

        /* sections */

        $classicSections = \DB::table('sectiontable')->get();
    
        foreach ($classicSections as $classicSection) {
            try {
                $newSection = new \Nexus\Section;
                $newSection->id = $classicSection->section_id;
                $newSection->title = $classicSection->section_title;
                $newSection->intro = $classicSection->section_intro;
                $newSection->user_id = $classicSection->user_id;
                $newSection->weight = $classicSection->section_weight;
                
                $newSection->save();

            } catch (\Exception $e) {
                $this->info('Upgrade failed on section ' . $classicSection->section_id . $e);
            }
            
        }

        // then loop through the sections again and add in the parent relationships
        foreach ($classicSections as $classicSection) {
            try {
                $newSection = \Nexus\Section::findOrFail($classicSection->section_id);
                $newSection->parent_id = $classicSection->parent_id;
                $newSection->save();

            } catch (\Exception $e) {
                $this->error('Upgrade failed on adding parent to section ' . $classicSection->section_id . $e);
            }
            
        }
        unset($classicSections);
        $this->info('Upgrade: Sections Complete');

        $this->info('Upgrade: Topics Start');

        $classicTopics = \DB::table('topictable')->get();

        foreach ($classicTopics as $classicTopic) {
            try {
                $newTopic = new \Nexus\Topic;
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
                $this->error('Upgrade failed on topic ' . $classicTopic->topic_id . $e);
            }

        }
        unset($classicTopics);
        $this->info('Upgrade: Topics Complete');

        \DB::table('messagetable')->chunk(1000, function($posts) {
            foreach ($posts as $classicPost) {
                $newPost = new \Nexus\Post;
                // \Log::info('Post: transferring  ' . $classicPost->message_id);

                $newPost->id                = $classicPost->message_id;
                $newPost->text              = $classicPost->message_text;
                $newPost->topic_id          = $classicPost->topic_id;
                $newPost->user_id           = $classicPost->user_id;
                $newPost->title             = $classicPost->message_title;
                $newPost->time              = $classicPost->message_time;
                $newPost->popname           = $classicPost->message_popname;
                $newPost->update_user_id    = $classicPost->update_user_id;

                if ($classicPost->message_html === 'n') {
                    $newPost->html = false;
                } else {
                    $newPost->html = true;
                }

                try {
                    $newPost->save();
                } catch (\Exception $e) {
                    $this->error('Upgrade: failed on post ' . $classicPost->message_id . $e);
                }
            }
        });

        $this->info('Upgrade: Posts Complete');

        $this->info('Upgrade: Complete');
    }
}
