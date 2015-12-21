<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('string_helper');
        $this->load->helper('text');
    }
    public function index()
    {

    }
    public function insert($value="")
    {
        switch ($value) {
            case 'story':
                $story_title = $this->input->post('name');
                $story_url = string_short(convert_accented_characters($story_title));
                $story_thumb = $this->input->post('img');
                $story_sumary = $this->input->post('sumary');
                $story_realname = $this->input->post('realname');
                $story_author = $this->input->post('author');
                $story_type = $this->input->post('type');
                $story_time = time();
                $sql = "INSERT INTO story(story_id, story_title, story_url, story_thumb, story_sumary, story_time, story_realname, story_author, story_type)
                        VALUES(NULL, '$story_title', '$story_url', '$story_thumb', '$story_sumary', $story_time, '$story_realname', '$story_author', '$story_type')";
                if(strlen($story_title) > 2) $this->db->query($sql);
                $result = $this->db->query("SELECT story_id FROM story WHERE story_url = '$story_url'")->result();
                echo $result[0]->story_id;
                break;
            case 'chapter':
                $story_id = $this->input->post('story');
                $chapter_title = $this->input->post('title');
                $chapter_url= string_short(convert_accented_characters($chapter_title));
                $chapter_content = $this->input->post('img');
                $chapter_time = time();
                $chapter_title = str_replace("Chương ", "", $chapter_title);
                $sql = "INSERT INTO chapter(chapter_id, story_id,  chapter_title, chapter_url, chapter_content, chapter_time)
                        VALUES(NULL, $story_id, $chapter_title, '$chapter_url', '$chapter_content', $chapter_time)";
                if(strlen($chapter_url) > 2) $this->db->query($sql);
                echo 'TRUE';
                break;
            default:
                # code...
                break;
        }
    }

}

/* End of file Ajax.php */
/* Location: ./application/controllers/Ajax.php */