<?php

namespace App\Services;

use App\Neuron\GenerateBlog;
use Illuminate\Support\Facades\Http;
use NeuronAI\Chat\Messages\UserMessage;
use Symfony\Component\DomCrawler\Crawler;

class BlogScraperService
{
    const URL = "https://laravel-news.com/blog";
    const LIMIT = 3;

    public function run()
    {
        $blogs = $this->getTopBlogs();
        
        if (empty($blogs)) {
            return [];
        }

        $savedBlogs = [];

        foreach ($blogs as $blog) {

            $blogUrl = $blog["url"];
            
            // Check if blog already exists
            if (\App\Models\Blog::where('url', $blogUrl)->exists()) {
                continue;
            }

            // Generate content
            $postDetail = GenerateBlog::make()->chat([
                new UserMessage($blogUrl)
            ]);

            $content = $postDetail->getContent();

            // Store in database
            $savedBlogs[] = \App\Models\Blog::create([
                'url' => $blogUrl,
                'content' => $content,
                'title' => $blog['title'],
                'image' => $blog['img'],
            ]);
        }

        return $savedBlogs;
    }

    private function getTopBlogs() {

        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
        ])->timeout(20)->get(self::URL);

        if($response->failed()) {
            return;
        }

        $blogsDetailUrl = [];
        $crawler = new Crawler($response->body());

        $crawler->filter('div[wire\\:key]')->each(function(Crawler $node) use (&$blogsDetailUrl) {

            if(count($blogsDetailUrl) >= self::LIMIT) {
                return;
            }

            $wireKey = $node->attr("wire:key");

            if (! str_starts_with($wireKey, 'article-')) {
                return;
            }

            if ($node->filter('a[href]')->count() === 0) {
                return;
            }

            $imageUrl = null;
            if ($node->filter('img[src]')->count() > 0) {
                $imageUrl = $node->filter('img[src]')->attr('src');
            }

             // Title from H3 (optional but expected)
            $title = null;
            if ($node->filter('h3')->count() > 0) {
                $title = trim($node->filter('h3')->first()->text());
            }
                    
            $url = $node->filter('a[href]')->attr('href');
            
            $blogsDetailUrl[] = [
                "title" =>  $title,
                "img"   =>  $imageUrl,
                "url"   =>  $url
            ];
        });

        return $blogsDetailUrl;
    }
}
