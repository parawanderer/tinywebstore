<?php
namespace App\Helpers;

class FFMPregHelper {
    private const SECONDS_OFFSET = 0;

    public static function saveThumbnail(string $mp4Path, string $outputPath) {
        $ffmpeg = \FFMpeg\FFMpeg::create();
        $video = $ffmpeg->open($mp4Path);
        $frame = $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(FFMPregHelper::SECONDS_OFFSET));
        $frame->save($outputPath);
    }
}