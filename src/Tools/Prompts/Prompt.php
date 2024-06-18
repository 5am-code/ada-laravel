<?php

namespace Ada\Tools\Prompts;

abstract class Prompt
{
    protected string $template = '';

    protected string $query = '';

    public function __construct()
    {
        $this->setDefaultPrompt();
    }

    public static function fromString(string $template): self
    {
        $prompt = new static();
        $prompt->setTemplate($template);

        return $prompt;
    }

    public static function fromFile(string $path): self
    {
        $promptBuilder = new static();
        $promptBuilder->setTemplateFromFile($path);

        return $promptBuilder;
    }

    public function setTemplateFromFile(string $path): self
    {
        $this->template = file_get_contents($path);

        return $this;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function setDefaultPrompt(): self
    {
        $this->template = view(config('ada.default_prompt_view', 'ada::default-prompt'));

        return $this;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setQuery(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function replaceInTemplate(string $key, string $value): self
    {
        $this->template = str_replace("{{$key}}", $value, $this->template);

        return $this;
    }

    abstract public function toArray(): array;
}
