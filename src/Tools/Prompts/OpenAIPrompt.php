<?php

namespace Ada\Tools\Prompts;

class OpenAIPrompt extends Prompt
{
    public function toArray(): array
    {
        return [
            [
                'role' => 'system',
                'content' => $this->template,
            ],
            [
                'role' => 'user',
                'content' => $this->query,
            ],
        ];
    }
}
