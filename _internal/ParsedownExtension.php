<?php

class ParsedownExtension extends Parsedown
{
    private $title = null;
    private $currLevel = 6;

    protected function blockHeader($line)
    {
        var_dump($this->currLevel);
        $block = Parsedown::blockHeader($line);

        $level = (integer)trim($block['element']['name'], 'h');
        if ($level < $this->currLevel)
        {
            $this->title = $block['element']['text'];
            $this->currLevel = $level;
        }

        return $block;
    }

    public function getTitle() : string | null
    {
        return $this->title;
    }
}