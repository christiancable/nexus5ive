<?php

namespace App\Console\Commands;

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
        $errorCount = 0;
        if (!\App\User::first()) {
            $count = \DB::select('select count(user_id) as count from usertable')[0]->count;
            $this->line("Found $count users ");
            $bar = $this->output->createProgressBar($count);
            $classicUsers = \DB::table('usertable')->get();
            
            foreach ($classicUsers as $classicUser) {
                $newUser = new \App\User;
                
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
                $emailUses = \DB::table('usertable')
                    ->select('user_id')->where('user_email', $classicUser->user_email)->get();
                $count = count($emailUses);
                if ($count > 1) {
                    $newUser->email = $classicUser->user_name . '@fakeemail.com';
                } else {
                    $newUser->email = $classicUser->user_email;
                }

                $newUser->password = $classicUser->user_password;
                
                if ($classicUser->user_realname != "") {
                    $newUser->name = $classicUser->user_realname;
                } else {
                    $newUser->name = "Unknown";
                }
                try {
                    $newUser->save();
                } catch (\Exception $e) {
                    $errorCount++;
                    \Log::error('Nexus:upgrade - Failed to add user '. $e);
                }
                $bar->advance();
            }

            $bar->finish();
            if ($errorCount) {
                $this->error("\nEncountered $errorCount errors. See log for details");
            }
            $this->info("\nUsers Complete\n");
            unset($classicUsers);
        } else {
            $this->error('Upgrade: found existing users - skipping Users');
        }
    }

    private function migrateComments()
    {
        $this->info('Importing Comments');
        $errorCount = 0;
        if (!\App\Comment::first()) {
            $count = \DB::select('select count(comment_id) as count from commenttable')[0]->count;
            $this->line("Found $count comments ");
            $bar = $this->output->createProgressBar($count);
            $classicComments = \DB::table('commenttable')->get();
            foreach ($classicComments as $classicComment) {
                $newComment = new \App\Comment;
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
                    $errorCount++;
                    \Log::error('Nexus:upgrade - Failed to add comment '. $e);
                }
            }
            $bar->finish();
            if ($errorCount) {
                $this->error("\nEncountered $errorCount errors. See log for details");
            }
            $this->info("\nComments Complete\n");
            unset($classicComments);
        } else {
            $this->error('Upgrade: found existing comments - skipping Comments');
        }
    }

    private function migrateSections()
    {
        $this->info('Importing Sections');
        $errorCount = 0;
        if (!\App\Section::first()) {
            $count = \DB::select('select count(section_id) as count from sectiontable')[0]->count;
            $this->line("Found $count sections ");
            $this->line("Migrating Sections ");
            $bar = $this->output->createProgressBar($count);
            $classicSections = \DB::table('sectiontable')->get();
        
            foreach ($classicSections as $classicSection) {
                try {
                    $newSection = new \App\Section;
                    $newSection->id = $classicSection->section_id;
                    $newSection->title = $classicSection->section_title;
                    $newSection->intro = $classicSection->section_intro;
                    $newSection->user_id = $classicSection->user_id;
                    $newSection->weight = $classicSection->section_weight;
                    
                    $newSection->save();
                    $bar->advance();
                } catch (\Exception $e) {
                    $errorCount++;
                    \Log::error('Nexus:upgrade - Failed to add section '. $e);
                }
            }

            $bar->finish();
            $this->line("\nMigration Complete");
            $this->line("Jumbling Sections into Subsections");
            $bar = $this->output->createProgressBar($count);

            foreach ($classicSections as $classicSection) {
                try {
                    $newSection = \App\Section::findOrFail($classicSection->section_id);
                    $newSection->parent_id = $classicSection->parent_id;
                    $newSection->save();
                    $bar->advance();
                } catch (\Exception $e) {
                    $errorCount++;
                    \Log::error('Nexus:upgrade - Failed to add parent to section '. $e);
                }
            }
            unset($classicSections);
            $bar->finish();
            if ($errorCount) {
                $this->error("\nEncountered $errorCount errors. See log for details");
            }
            $this->info("\nSections Complete\n");
        } else {
            $this->error('Upgrade: found existing sections - skipping Sections');
        }
    }


    private function migrateTopics()
    {
        $this->info('Importing Topics');
        $errorCount = 0;
        if (!\App\Topic::first()) {
            $count = \DB::select('select count(topic_id) as count from topictable')[0]->count;
            $this->line("Found $count topics");

            $bar = $this->output->createProgressBar($count);
            $classicTopics = \DB::table('topictable')->get();
        
            foreach ($classicTopics as $classicTopic) {
                $newTopic = new \App\Topic;
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
                    
                try {
                    $newTopic->save();
                    $bar->advance();
                } catch (\Exception $e) {
                    $errorCount++;
                    \Log::error('Nexus:upgrade - Failed to import topic: '. $e);
                }
            }

            $bar->finish();
            unset($classicTopics);
            if ($errorCount) {
                $this->error("\nFailed to import $errorCount posts. See log for details");
            }
            $this->info("\nTopics Complete\n");
        } else {
            $this->error('Upgrade: found existing topics - skipping Topics');
        }
    }



    private function migratePosts()
    {
        $this->info('Importing Posts');

        if (!\App\Post::first()) {
            $errorCount = 0;
            $count = \DB::select('select count(message_id) as count from messagetable')[0]->count;
            $this->line("Found $count posts");
            $bar = $this->output->createProgressBar($count);
            \DB::table('messagetable')->chunk(1000, function ($posts) use (&$errorCount, &$count, &$bar) {
                foreach ($posts as $classicPost) {
                    $newPost = new \App\Post;
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
                        $errorCount++;
                        \Log::info('Nexus:upgrade - Failed to import post: '. $e);
                    }
                }
            });
            $bar->finish();
            if ($errorCount) {
                $this->error("\nFailed to import $errorCount posts. See log for details");
            }
            $this->info("\nPosts Complete\n");
        } else {
            $this->error('Upgrade: found existing posts - skipping Posts');
        }
    }



    private function migrateViews()
    {
        $this->info('Importing Views');

        if (!\App\View::first()) {
            $errorCount = 0;
            $count = \DB::select('select count(topicview_id) as count from topicview')[0]->count;
            $this->line("Found $count views");
            $bar = $this->output->createProgressBar($count);
            \DB::table('topicview')->chunk(1000, function ($views) use (&$errorCount, &$count, &$bar) {

                foreach ($views as $classicView) {
                    $newView = new \App\View;
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
                         $errorCount++;
                        \Log::info('Nexus:upgrade - Failed to import view: '. $e);
                    }
                }
            });
            $bar->finish();
            if ($errorCount) {
                $this->error("\nFailed to import $errorCount views. See log for details");
            }
            $this->info("\nViews Complete\n");
        } else {
            $this->error('Upgrade: found existing views - skipping Posts');
        }
    }


    private function migrateMessages()
    {
        $this->info('Importing Mesages');

        if (!\App\Message::first()) {
            $errorCount = 0;
            $count = \DB::select('select count(nexusmessage_id) as count from nexusmessagetable')[0]->count;
            $this->line("Found $count messages");
            $bar = $this->output->createProgressBar($count);
            \DB::table('nexusmessagetable')->chunk(1000, function ($messages) use (&$errorCount, &$count, &$bar) {

                foreach ($messages as $classicMessage) {
                    $newMessage = new \App\Message;
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
                         $errorCount++;
                        \Log::info('Nexus:upgrade - Failed to import message: '. $e);
                    }
                }
            });
            $bar->finish();
            if ($errorCount) {
                $this->error("\nFailed to import $errorCount messages. See log for details");
            }
            $this->info("\nMessages Complete\n");
        } else {
            $this->error('Upgrade: found existing messages - skipping Messages');
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
        $this->migrateTopics();
        $this->migratePosts();
        $this->migrateViews();
        $this->migrateMessages();


        $this->info('Upgrade: Complete');
    }
}
