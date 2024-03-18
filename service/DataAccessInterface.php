<?php

namespace service;
interface DataAccessInterface
{
    public function getAllAnnonces();

    public function getPost($id);

    public function createAnnonce($login, $info);
}