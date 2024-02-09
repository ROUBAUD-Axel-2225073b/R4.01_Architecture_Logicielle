<?php
namespace service;

interface DataAccessInterface
{
    public function getAllAnnonces();
    public function getPost($id);
    public function getUser($login, $password);
    public function addUser($login, $password, $name, $surname);
    public function userExists($login);
    public function createPost($title, $body, $date);
    public function isAdmin($login);
    public function deleteUser($login);
    public function updatePost($id, $title, $body, $date);
}