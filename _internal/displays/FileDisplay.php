<?php

interface FileDisplay
{
    public function doesHandle(string $filename, string $ext): bool;
}