<?php

namespace App\Services;

class ReelProductionService
{
    public function processReelData(array $input): array
    {
        $scenes = $input['scenes'] ?? [];
        $music = $input['music'] ?? 'cinematic';

        $imagePrompts = array_map(function($scene) {
            return $this->generateImagePrompt($scene);
        }, $scenes);

        $totalDuration = 60; // default total duration in seconds
        $durationPerScene = $totalDuration / count($scenes);

        return [
            'image_prompts' => $imagePrompts,
            'video_plan' => [
                'duration_per_scene' => $durationPerScene . ' seconds',
                'transition' => 'smooth fade',
                'caption_style' => 'bold white subtitle at bottom',
                'music_type' => $music
            ]
        ];
    }

    private function generateImagePrompt(string $scene): string
    {
        $basePrompt = $scene;

        $qualifiers = [
            'vertical 9:16 aspect ratio',
            'cinematic lighting',
            'ultra realistic 4K quality',
            'no text or watermarks',
            'no logos',
            'sharp focus no blur',
            'safe for all audiences'
        ];

        return $basePrompt . ', ' . implode(', ', $qualifiers) . ', stable diffusion';
    }
}
