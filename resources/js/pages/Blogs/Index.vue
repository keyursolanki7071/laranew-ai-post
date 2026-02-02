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

    <div class="min-h-screen bg-gray-50/50">
        <!-- Hero Section -->
        <div class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
                <div class="lg:flex lg:items-center lg:justify-between">
                    <div class="max-w-2xl">
                        <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl tracking-tight">
                            AI Content Generator
                        </h1>
                        <p class="mt-4 text-xl text-gray-500">
                            Transforming Laravel News into engaging LinkedIn posts with the power of AI.
                        </p>
                    </div>
                    <div class="mt-8 lg:mt-0 lg:ml-8 flex items-center gap-4">
                        <button 
                            @click="fetchBlogs"
                            :disabled="isFetching"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg shadow-indigo-200 hover:shadow-indigo-300 transform hover:-translate-y-0.5 active:translate-y-0"
                        >
                            <svg v-if="isFetching" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            {{ isFetching ? 'Generating Content...' : 'Generate New Posts' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Empty State -->
            <div v-if="blogs.length === 0" class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300">
                <div class="mx-auto h-24 w-24 text-gray-300 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="mt-2 text-xl font-medium text-gray-900">No posts generated yet</h3>
                <p class="mt-2 text-gray-500 max-w-sm mx-auto">Get started by clicking the "Generate New Posts" button above to fetch and process the latest articles.</p>
            </div>

            <!-- Grid -->
            <transition-group 
                tag="div" 
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
                class="grid gap-8 md:grid-cols-2 lg:grid-cols-3"
            >
                <div v-for="blog in blogs" :key="blog.id" 
                    class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col h-full overflow-hidden"
                >
                    <!-- Image -->
                    <div class="relative h-56 w-full overflow-hidden bg-gray-100">
                        <img 
                            v-if="blog.image" 
                            :src="blog.image" 
                            :alt="blog.title || 'Blog Image'" 
                            class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out"
                        >
                        <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        
                        <!-- Top overlay gradient -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <!-- Date Badge -->
                        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-semibold text-gray-700 shadow-sm">
                            {{ new Date(blog.created_at).toLocaleDateString(undefined, { month: 'short', day: 'numeric' }) }}
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-indigo-600 transition-colors duration-200">
                            <Link :href="route('blogs.show', blog.id)">
                                {{ blog.title || 'Untitled Post' }}
                            </Link>
                        </h3>

                        <p class="text-gray-500 line-clamp-3 mb-6 flex-1 text-sm leading-relaxed">
                            {{ blog.content }}
                        </p>

                        <!-- Actions -->
                        <div class="pt-5 border-t border-gray-50 flex items-center justify-between">
                            <Link :href="route('blogs.show', blog.id)" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 flex items-center group/link">
                                Read Post
                                <svg class="w-4 h-4 ml-1 transform group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </Link>
                            
                            <div class="flex items-center gap-2">
                                <button 
                                    v-if="blog.image"
                                    @click="downloadImage(blog.image)"
                                    class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200 tooltip-trigger"
                                    title="Download Image"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </button>
                                <button 
                                    @click="copyToClipboard(blog.url)"
                                    class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200"
                                    title="Copy Link"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                </button>
                                <button 
                                    @click="copyToClipboard(blog.content)"
                                    class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200"
                                    title="Copy Content"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition-group>
        </div>
    </div>
</template>
