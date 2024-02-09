<?php

namespace domaine;
class Post
{
    private $id;
    private $title;
    private $body;
    private $date;

    public function __construct($id, $title, $body, $date)
    {
        $this->id = $id;
        $this->title = $title;
        $this->body = $body;
        $this->date = $date;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getDate()
    {
        return $this->date;
    }
}