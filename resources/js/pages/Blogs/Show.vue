<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { marked } from 'marked';
import DOMPurify from 'dompurify';
import { computed } from 'vue';

const props = defineProps<{
    blog: {
        id: number;
        url: string;
        title: string | null;
        image: string | null;
        content: string;
        created_at: string;
    };
}>();

const renderedContent = computed(() => {
    const rawHtml = marked.parse(props.blog.content, { breaks: true });
    return DOMPurify.sanitize(rawHtml as string);
});

const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
};

const downloadImage = async (imageUrl: string) => {
    try {
        const proxyUrl = route('blogs.proxy-image', { url: imageUrl });
        const response = await fetch(proxyUrl);
        const blob = await response.blob();
        
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'blog-image.jpg';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    } catch (err) {
        console.error('Failed to download image:', err);
    }
};
</script>

<template>
    <Head :title="`Post Details #${blog.id}`" />

    <div class="min-h-screen bg-gray-50/50">
        <!-- Navigation Bar -->
        <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <Link :href="route('blogs.index')" class="text-gray-500 hover:text-indigo-600 font-medium inline-flex items-center transition-colors">
                            <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Back to Posts
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden ring-1 ring-gray-900/5">
                <!-- Image Header -->
                <div v-if="blog.image" class="relative w-full h-80 md:h-96">
                    <img :src="blog.image" :alt="blog.title || 'Blog Image'" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900/60 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-8 text-white">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-600/90 text-white backdrop-blur-sm mb-4">
                            Generated Post
                        </span>
                        <h1 class="text-3xl md:text-4xl font-bold leading-tight text-shadow">
                            {{ blog.title || blog.url }}
                        </h1>
                    </div>
                </div>

                <div class="p-8 md:p-12">
                    <!-- Meta info if no image header -->
                    <div v-if="!blog.image" class="mb-8 border-b border-gray-100 pb-8">
                        <div class="flex items-center gap-4 mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                                Generated Post
                            </span>
                            <span class="text-sm text-gray-500">
                                {{ new Date(blog.created_at).toLocaleString() }}
                            </span>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 break-words">
                            {{ blog.title || blog.url }}
                        </h1>
                    </div>

                    <!-- Actions Bar (Top) -->
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-8 p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <a 
                            :href="blog.url" 
                            target="_blank" 
                            class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors"
                        >
                            Read Original Article
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>

                        <div class="flex items-center gap-2">
                             <button 
                                v-if="blog.image"
                                @click="downloadImage(blog.image)"
                                class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-white rounded-lg transition-all border border-transparent hover:border-gray-200 hover:shadow-sm cursor-pointer"
                                title="Download Image"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </button>
                            <button 
                                @click="copyToClipboard(blog.url)"
                                class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-white rounded-lg transition-all border border-transparent hover:border-gray-200 hover:shadow-sm cursor-pointer"
                                title="Copy Source Link"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div 
                        class="prose prose-lg prose-indigo max-w-none prose-headings:font-bold prose-headings:text-gray-900 prose-p:text-gray-600 prose-p:leading-relaxed"
                        v-html="renderedContent"
                    ></div>
                </div>

                <!-- Footer Actions -->
                <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-sm text-gray-500">
                        Generated on {{ new Date(blog.created_at).toLocaleString() }}
                    </span>
                    <button 
                        @click="copyToClipboard(blog.content)"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5 cursor-pointer"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                        Copy Post Content (Markdown)
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
