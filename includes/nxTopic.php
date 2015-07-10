<?php
namespace nexusfive;

use Exception;

class NxTopic
{
 
    public $topic_id;
    public $topic_title;
    public $section_id;
    public $topic_description;
    public $topic_annon;
    public $topic_readonly;
    public $topic_weight;
    public $topic_title_hidden;

    public $postCount;

    public $section;


    private $cfg;
    private $data;
    private $ui;

    // we need section info too for determining who owns the topic

    // give the amount of time we're referencing the user here it might be an idea to pass it in the constructor

    public function __construct($topic_id, $cfg = false, $data = false, $ui = false)
    {

        if ($cfg === false) {
            $globalConfig = new NxConfig();
            $this->cfg = $globalConfig->getConfig();
        } else {
            $this->cfg = $cfg;
        }

        if ($data === false) {
            $data = new NxConfig();
            $this->data = $datastore = new NxData($this->cfg);
        } else {
            $this->data = $data;
        }

        if ($ui === false) {
            $this->ui = new NxInterface($this->cfg);
        } else {
            $this->ui = $ui;
        }

        if (!$topicData = $this->data->readTopicInfo($topic_id)) {
            throw new Exception('Invalid Topic');
        }

        foreach ($topicData as $key => $value) {
            $this->$key = $value;
        }

        if (!$sectionData = $this->data->readSectionInfo($this->section_id)) {
            throw new Exception('Invalid Section');
        }

        $this->section = $sectionData;
        $this->postCount = $this->data->countPostsInTopic($topic_id);

        // count new posts in the topic here
      
    }

    public function readTopic($startPost, $user_id)
    {
        // update the last time read for the topic
        $this->data->updateTopicLatestReadTime($this->topic_id, $user_id);

        $countPostsSinceLastRead = '';
        $countPostsPerPage = '';


        $countTotalPostsInTopic = $this->postCount;


        // 

        // if no start post specified

        // if start post specified



        // return the posts
        return $this->data->getPostsInTopic($this->topic_id, $startPost, $numberOfPosts);
    }

    public function countNewPosts($user_id)
    {
        return $this->data->countNewPostsInTopic($this->topic_id, $user_id);
    }
}
