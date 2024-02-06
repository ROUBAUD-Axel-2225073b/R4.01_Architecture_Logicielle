<?php
namespace service;

interface DataAccessInterface
{
    public function getAllAnnonces();
    public function getPost($id);
    public function getUser($login, $password);
    public function addUser($login, $password, $name, $surname);
    public function addPost($title, $body);
}