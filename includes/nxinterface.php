<?php 

namespace nexusfive;

use Twig_Environment;
use Twig_Loader_Filesystem;

/* one day this will all be TWIG */

/* I've decided that redirecion is a user interface function */

class NxInterface
{

    private $cfg;

    public function __construct($cfg = false)
    {
        $this->cfg = $cfg;
    }

    public function ejectUser()
    {
        $webRoot = $this->cfg['webRoot'];
        Header("Location: " . $webRoot . "/");
        die();
    }

    public function renderMessages($templateData)
    {
        /*
        $templateData = array(
            'messages'      => $messages,
            'recipient'     => $recipent,
            'currentUser'   => $user_array,
            'users'         => $users,
            'page'          => $page
            );
        */

        $templateData['breadcrumbs'] = array();
        $templateData['breadcrumbs'][] = array(
            'title'         => 'Home' ,
            'location'      => $this->cfg['webRoot']
            );
        $templateData['breadcrumbs'][] = array(
            'title'         => 'Messages' ,
            'location'      => ''
            );
    
        // $templateData['navigation'] goes here?
        
        $templateData['page'] =  array (
            'title'     => 'Messages',
            'root'      => $this->cfg['webRoot']
            );

        $loader = new Twig_Loader_Filesystem($this->cfg['viewsLocation']);
        $twig = new Twig_Environment($loader);

        return $twig->render('messages.twig', $templateData);
    }
   
    public function redirectToMessages()
    {
        $webRoot = $this->cfg['webRoot'];
        Header("Location: " . $webRoot . "/messages/");
        die();
    }


    public function getBreadcrumbs($section_info)
    {
        // accepts an array of section info and returns HTML for breadcrumbs

        $HTML = <<<'HTML'
<font size="-1"><a href="section.php?section_id=%SECTION_ID%">%SECTION_NAME%</a> -&gt; </font>
HTML;

        $HTML = str_replace('%SECTION_NAME%', $section_info['section_title'], $HTML);
        $HTML = str_replace('%SECTION_ID%', $section_info['section_id'], $HTML);

        return $HTML;
    }
}
