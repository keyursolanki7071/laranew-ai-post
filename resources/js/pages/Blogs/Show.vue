<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

defineProps<{
    blog: {
        id: number;
        url: string;
        title: string | null;
        image: string | null;
        content: string;
        created_at: string;
    };
}>();

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

    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <Link :href="route('blogs.index')" class="text-indigo-600 hover:text-indigo-500 font-medium inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to List
                </Link>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div v-if="blog.image" class="w-full h-64 md:h-96 overflow-hidden">
                    <img :src="blog.image" :alt="blog.title || 'Blog Image'" class="w-full h-full object-cover">
                </div>

                <div class="p-8 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <span class="px-3 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 rounded-full">
                            Generated Post
                        </span>
                        <span class="text-sm text-gray-500">
                            Generated on {{ new Date(blog.created_at).toLocaleString() }}
                        </span>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-4 break-words">
                        {{ blog.title || blog.url }}
                    </h1>
                    
                    <a :href="blog.url" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-500 inline-flex items-center">
                        Visit Original Article 
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                </div>

                <div class="p-8 bg-gray-50/50">
                    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                        <div class="prose max-w-none text-gray-800 whitespace-pre-line leading-relaxed">
                            {{ blog.content }}
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-gray-50 border-t border-gray-100 flex flex-wrap justify-end gap-3">
                    <button 
                        v-if="blog.image"
                        @click="downloadImage(blog.image)"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download Image
                    </button>
                    
                    <button 
                        @click="copyToClipboard(blog.url)"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        Copy Source URL
                    </button>

                    <button 
                        @click="copyToClipboard(blog.content)"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                        Copy Content
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
