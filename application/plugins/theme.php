<?php

class Theme_plugin extends Plugin
{
    public function partial()
    {
        $data = $this->attributes();
        unset($data['name']);

        return theme_partial($this->attribute('name'), $data);
    }
}

