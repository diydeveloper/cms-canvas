<?php

class Secure_plugin extends Plugin
{
    public function is_auth()
    {
        return ($this->secure->is_auth()) ? 1 : 0;
    }
}


