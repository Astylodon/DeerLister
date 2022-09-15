<?php

interface FileDisplay
{
    public function doesHandle(string $ext): bool;
}