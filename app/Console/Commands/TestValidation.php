<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestValidation extends Command
{
    protected $signature = 'test:validation';
    protected $description = 'Test validation rules';

    public function handle()
    {
        $this->info('Testing Validation Rules');

        // Test 1: All valid
        $this->line('');
        $this->info('Test 1: Valid data (topic, mood, duration)');
        $validator = validator([
            'topic' => 'Nature Documentary',
            'mood' => 'cinematic',
            'duration' => 15
        ], [
            'topic' => 'required|string|max:200',
            'mood' => 'required|string',
            'duration' => 'required|integer|in:15,30,60,90',
        ]);

        if ($validator->fails()) {
            $this->error('FAILED: ' . json_encode($validator->errors()->toArray()));
        } else {
            $this->line('<fg=green>PASSED</>');
        }

        // Test 2: Missing topic
        $this->line('');
        $this->info('Test 2: Missing topic');
        $validator = validator([
            'mood' => 'cinematic',
            'duration' => 15
        ], [
            'topic' => 'required|string|max:200',
            'mood' => 'required|string',
            'duration' => 'required|integer|in:15,30,60,90',
        ]);

        if ($validator->fails()) {
            $this->line('<fg=green>VALIDATION CAUGHT ERROR:</>');
            $this->line(json_encode($validator->errors()->toArray(), JSON_PRETTY_PRINT));
        } else {
            $this->error('FAILED: Should have caught missing topic');
        }

        // Test 3: Invalid duration (not in list)
        $this->line('');
        $this->info('Test 3: Invalid duration (42, should be 15,30,60,90)');
        $validator = validator([
            'topic' => 'Nature',
            'mood' => 'cinematic',
            'duration' => 42
        ], [
            'topic' => 'required|string|max:200',
            'mood' => 'required|string',
            'duration' => 'required|integer|in:15,30,60,90',
        ]);

        if ($validator->fails()) {
            $this->line('<fg=green>VALIDATION CAUGHT ERROR:</>');
            $this->line(json_encode($validator->errors()->toArray(), JSON_PRETTY_PRINT));
        } else {
            $this->error('FAILED: Should have caught invalid duration');
        }

        // Test 4: Empty mood
        $this->line('');
        $this->info('Test 4: Empty mood');
        $validator = validator([
            'topic' => 'Nature',
            'mood' => '',
            'duration' => 15
        ], [
            'topic' => 'required|string|max:200',
            'mood' => 'required|string',
            'duration' => 'required|integer|in:15,30,60,90',
        ]);

        if ($validator->fails()) {
            $this->line('<fg=green>VALIDATION CAUGHT ERROR:</>');
            $this->line(json_encode($validator->errors()->toArray(), JSON_PRETTY_PRINT));
        } else {
            $this->error('FAILED: Should have caught empty mood');
        }

        $this->info('');
        $this->info('✓ Validation testing complete');
    }
}
