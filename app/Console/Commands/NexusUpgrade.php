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

    private function migrateUsers()
    {
        $this->info('Importing Users');
        
        if (!\Nexus\User::first()) {
            $count = \DB::select('select count(user_id) as count from usertable')[0]->count;
            $this->line("Found $count users ");
            $bar = $this->output->createProgressBar($count);
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
                
                if ($classicUser->user_realname != "") {
                    $newUser->name = $classicUser->user_realname;
                } else {
                    $newUser->name = "Unknown";
                }
                
                $newUser->save();
                $bar->advance();
            }

            $bar->finish();
            $this->info("\nUsers Complete\n");
            unset($classicUsers);
        } else {
            $this->error('Upgrade: found existing users - skipping Users');
        }
    }

    private function migrateComments()
    {
        $this->info('Importing Comments');
        
        if (!\Nexus\Comment::first()) {
            $count = \DB::select('select count(comment_id) as count from commenttable')[0]->count;
            $this->line("Found $count comments ");
            $bar = $this->output->createProgressBar($count);
            $classicComments = \DB::table('commenttable')->get();
            foreach ($classicComments as $classicComment) {
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
                try {
                    $newComment->save();
                    $bar->advance();
                } catch (\Exception $e) {
                    $this->error('Failed on comment ' . $classicComment->comment_id);
                }
            }
            $bar->finish();
            $this->info("\nComments Complete\n");
            unset($classicComments);
        } else {
            $this->error('Upgrade: found existing comments - skipping Comments');
        }
    }

    private function migrateSections()
    {
        $this->info('Importing Sections');
        
        if (!\Nexus\Section::first()) {
            $count = \DB::select('select count(section_id) as count from sectiontable')[0]->count;
            $this->line("Found $count sections ");
            $this->line("Migrating Sections ");
            $bar = $this->output->createProgressBar($count);
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
                    $bar->advance();

                } catch (\Exception $e) {
                    $this->info('Upgrade failed on section ' . $classicSection->section_id);
                }
                
            }

            $bar->finish();
            $this->line("\nMigration Complete");
            $this->line("Jumbling Sections into Subsections");
            $bar = $this->output->createProgressBar($count);

            foreach ($classicSections as $classicSection) {
                try {
                    $newSection = \Nexus\Section::findOrFail($classicSection->section_id);
                    $newSection->parent_id = $classicSection->parent_id;
                    $newSection->save();
                    $bar->advance();
                } catch (\Exception $e) {
                    $this->error('Upgrade failed on adding parent to section ' . $classicSection->section_id);
                }
                
            }
            unset($classicSections);
            $bar->finish();
            $this->info("\nSections Complete\n");
        } else {
            $this->error('Upgrade: found existing sections - skipping Sections');
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Starting Migration');
        $this->info('==================');

        $this->migrateUsers();
        $this->migrateComments();
        $this->migrateSections();
        die();
    
        
    

     

            // then loop through the sections again and add in the parent relationships
      
        

        $this->info('Upgrade: Topics Start');

        $existingTopicsCount = \Nexus\Topic::all()->count();

        if (!$existingTopicsCount) {
            $classicTopics = \DB::table('topictable')->get();
            $bar = $this->output->createProgressBar($existingTopicsCount);

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
                $bar->advance();

            }
            $bar->finish();
            unset($classicTopics);
            $this->info('Upgrade: Topics Complete');
        } else {
            $this->info('Upgrade: found existing topics - skipping Topics');
        }
        

        $this->info('Upgrade: Posts Start');

        $existingPostsCount = \DB::select('select count(message_id) from messagetable');
        $bar = $this->output->createProgressBar($existingPostsCount);

        if (!$existingPostsCount) {
            \DB::table('messagetable')->chunk(1000, function($posts) {
                foreach ($posts as $classicPost) {
                    $newPost = new \Nexus\Post;
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
                         $bar->advance();
                    } catch (\Exception $e) {
                        $this->error('Upgrade: failed on post ' . $classicPost->message_id . $e);
                    }
                }
            });

            $bar->finish();
            $this->info('Upgrade: Posts Complete');
        } else {
            $this->info('Upgrade: found existing posts - skipping Posts');
        }

        $this->info('Upgrade: Views Start');

        $existingViewsCount = \DB::select('select count(topicview_id) from topicview');
        $bar = $this->output->createProgressBar($existingViewsCount);

        if (!$existingViewsCount) {
            \DB::table('topicview')->chunk(1000, function($views) {
                foreach ($views as $classicView) {
                    $newView = new \Nexus\View;
                    $newView->id                    = $classicView->topicview_id;
                    $newView->user_id               = $classicView->user_id;
                    $newView->topic_id              = $classicView->topic_id;
                    $newView->latest_view_date      = $classicView->msg_date;

                    if ($classicView->unsubscribe === 0) {
                        $newView->unsubscribed = false;
                    } else {
                        $newView->unsubscribed = true;
                    }

                    try {
                        $newView->save();
                        $bar->advance();
                    } catch (\Exception $e) {
                        $this->error('Upgrade: failed on view ' . $classicView->topicview_id . $e);
                    }
                }
            });
            $bar->advance();
            $this->info('Upgrade: Views Complete');
        } else {
            $this->info('Upgrade: found existing views - skipping Views');
        }

         $this->info('Upgrade: Messages Start');

        $existingMessagesCount = \DB::select('select count(nexusmessage_id) from nexusmessagetable');
        $bar = $this->output->createProgressBar($existingMessagesCount);

        if (!$existingMessagesCount) {
            \DB::table('nexusmessagetable')->chunk(1000, function($messages) {
                foreach ($messages as $classicMessage) {
                    $newMessage = new \Nexus\Message;
                    $newMessage->id                    = $classicMessage->nexusmessage_id;
                    $newMessage->user_id               = $classicMessage->user_id;
                    $newMessage->author_id             = $classicMessage->from_id;
                    $newMessage->text                  = $classicMessage->text;
                    $newMessage->time                  = $classicMessage->time;

                    if ($classicMessage->readstatus === 'n') {
                        $newMessage->read = false;
                    } else {
                        $newMessage->read = true;
                    }

                    try {
                        $newMessage->save();
                        $bar->advance();
                    } catch (\Exception $e) {
                        $this->error('Upgrade: failed on message ' . $classicMessage->nexusmessage_id . $e);
                    }
                }
            });
            $bar->finish();
            $this->info('Upgrade: Messages Complete');
        } else {
            $this->info('Upgrade: found existing messages - skipping Messages');
        }

        $this->info('Upgrade: Complete');
    }
}
