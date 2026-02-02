<?php

declare(strict_types=1);

namespace App\Neuron;

use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Anthropic;
use NeuronAI\Providers\Gemini\Gemini;
use NeuronAI\SystemPrompt;
use NeuronAI\Tools\ToolInterface;
use NeuronAI\Tools\Toolkits\ToolkitInterface;

class GenerateBlog extends Agent
{
    protected function provider(): AIProviderInterface
    {
        return new Gemini(
            key: config('services.gemini.key'),
            model: "gemini-2.5-flash-preview-09-2025"
        );
    }

    public function instructions(): string
    {
        return (string) new SystemPrompt(
            background: [
                "
                    You are a LinkedIn blog post creator. You will create a LinkedIn post from the URL which i will provide you. You task is to generate simple post based on the content in that URL. But post should sound like i have read that blog and writing that blog myself and it should cover all details mentioned in that blog. Also mention the source from where i got these detail.

                    Output:
                    Return only the LinkedIn post text, as if I wrote it myself.
                    "
                ],
        );
    }

    /**
     * @return ToolInterface[]|ToolkitInterface[]
     */
    protected function tools(): array
    {
        return [];
    }
}
