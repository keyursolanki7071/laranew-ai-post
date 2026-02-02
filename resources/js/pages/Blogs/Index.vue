<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';

defineProps<{
    blogs: Array<{
        id: number;
        url: string;
        title: string | null;
        image: string | null;
        content: string;
        created_at: string;
    }>;
}>();

const isFetching = ref(false);

const fetchBlogs = () => {
    isFetching.value = true;
    router.post(route('blogs.fetch'), {}, {
        onFinish: () => {
            isFetching.value = false;
        }
    });
};

const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    // Optional: Add toast notification here
};

const downloadImage = async (imageUrl: string) => {
    try {
        const proxyUrl = route('blogs.proxy-image', { url: imageUrl });
        const response = await fetch(proxyUrl);
        const blob = await response.blob();
        
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'blog-image.jpg'; // We could extract name from url if needed
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
    <Head title="Generated Linked Posts" />

    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Laravel News AI Posts
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        AI-generated LinkedIn content from latest blogs.
                    </p>
                </div>
                
                <button 
                    @click="fetchBlogs"
                    :disabled="isFetching"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <svg v-if="isFetching" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    {{ isFetching ? 'Fetching...' : 'Fetch New Blogs' }}
                </button>
            </div>

            <div v-if="blogs.length === 0" class="text-center bg-white p-12 rounded-xl shadow-sm border border-gray-100">
                <p class="text-gray-500">No posts generated yet.</p>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="blog in blogs" :key="blog.id" 
                    class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden border border-gray-100 flex flex-col h-full">
                    
                    <div v-if="blog.image" class="h-48 w-full overflow-hidden">
                        <img :src="blog.image" :alt="blog.title || 'Blog Image'" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500">
                    </div>

                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-3">
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                {{ new Date(blog.created_at).toLocaleDateString() }}
                            </span>
                        </div>
                        
                        <h3 class="text-base font-semibold text-gray-900 mb-2 line-clamp-2" :title="blog.title || blog.url">
                            {{ blog.title || blog.url }}
                        </h3>

                        <p class="text-sm text-gray-500 line-clamp-3 mb-4 flex-1">
                            {{ blog.content }}
                        </p>

                        <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between gap-2">
                            <Link :href="route('blogs.show', blog.id)" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                Details
                            </Link>
                            
                            <div class="flex items-center gap-1">
                                <button 
                                    v-if="blog.image"
                                    @click="downloadImage(blog.image)"
                                    class="p-1.5 text-gray-400 hover:text-indigo-600 transition-colors rounded-full hover:bg-gray-50"
                                    title="Download Image"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </button>
                                <button 
                                    @click="copyToClipboard(blog.url)"
                                    class="p-1.5 text-gray-400 hover:text-indigo-600 transition-colors rounded-full hover:bg-gray-50"
                                    title="Copy Source URL"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                </button>
                                <button 
                                    @click="copyToClipboard(blog.content)"
                                    class="p-1.5 text-gray-400 hover:text-indigo-600 transition-colors rounded-full hover:bg-gray-50"
                                    title="Copy Content"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
