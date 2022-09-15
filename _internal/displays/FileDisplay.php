<?php

interface FileDisplay
{
    /**
     * Whether the current file display handles this file
     *
     * @param string $ext The file extension without leading dot
     *
     * @return bool Whether the file preview handles the file
     */
    public function doesHandle(string $ext): bool;
}