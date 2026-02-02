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
            model: "gemini-2.5-flash"
        );
    }

    public function instructions(): string
    {
        return (string) new SystemPrompt(
            background: [
                "
                    You are helping me write a LinkedIn post after I personally read the blog at the URL which i will provide you.

                    How to write:
                    - Write in first person (“I”)
                    - The post should feel like my own reflection after reading the blog
                    - Do NOT try to summarize everything
                    - Select only the most meaningful 2–3 insights
                    - Use your own words, not the blog’s wording
                    - The tone should be natural, thoughtful, and professional
                    - Avoid marketing language and AI-sounding phrases

                    Structure:
                    - Start with a personal hook (what caught my attention or made me think)
                    - Share 2–3 short reflections or learnings
                    - End with a genuine question for discussion

                    Constraints:
                    - 80–120 words
                    - No emojis
                    - Do NOT mention the author or website name inside the post
                    - Do NOT include the blog URL in the post text
                    - Do NOT say “this blog says” or “this article explains”

                    At the end, add a separate line:
                    Source: URL

                    Output:
                    Return only the LinkedIn post text.
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
