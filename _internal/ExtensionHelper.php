<?php

namespace DeerLister;

class ExtensionHelper
{
    public static function getAudioExtensions() : array
    {
        return ["mp3", "wav", "ogg", "webm", "flac"];
    }

    public static function getImageExtensions() : array
    {
        return ["apng", "png", "jpg", "jpeg", "gif", "svg", "webp"];
    }
}