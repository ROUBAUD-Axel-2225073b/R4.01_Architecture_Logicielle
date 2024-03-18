<?php

namespace service;

class AnnonceCreation
{
    public function createAnnonce($login, $info, $data)
    {
        return $data->createAnnonce($login, $info) != false;
    }

}