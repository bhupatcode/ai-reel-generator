<?php

namespace App\Services;

class ImageGenerationService
{
    /**
     * Generate an image URL using Pollinations.ai (Free, Unlimited)
     * Returns a direct and reliable image URL for the frontend
     */
    public function generateFromPrompt(string $prompt): string
    {
        // Pollinations.ai is free and unlimited.
        // Returning the URL directly to the frontend allows the user's browser to fetch it,
        // which bypasses server-side IP blocking and is much more efficient.
        $width = 720;
        $height = 1280;
        $seed = rand(1, 2147483647); // Use a much larger seed range
        
        // Add random stylistic variations to avoid repetitive looks
        $variations = [
            'cinematic', 'photorealistic', 'vibrant', 'dramatic lighting', 
            'high detail', 'artistic', 'masterpiece', '8k resolution',
            'epic composition', 'soft bokeh', 'hyper-detailed', 'cyberpunk style',
            'minimalist aesthetic', 'vintage film look', 'natural lighting', 'studio professional'
        ];
        $style = $variations[array_rand($variations)];
        $enhancedPrompt = $prompt . ", " . $style . ", highly detailed, 9:16 aspect ratio, mobile reel style";
        
        $encodedPrompt = urlencode($enhancedPrompt);
        // Use the stable default model on Pollinations
        return "https://image.pollinations.ai/prompt/{$encodedPrompt}?width={$width}&height={$height}&seed={$seed}&nologo=true";
    }
}
