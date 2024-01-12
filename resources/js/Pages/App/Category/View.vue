<script setup>
import {ref} from "vue";
import { Head, Link } from '@inertiajs/vue3';
import DefaultLayout from "@/App/Layouts/DefaultLayout.vue";
import BlogPostPreview from "@/App/Components/BlogPostPreview.vue";
import {TailwindPagination} from 'laravel-vue-pagination';

const laravelData = ref({});
const { posts, category } = defineProps(
    ['posts', 'category'],
    context
);

const getResults = async (page = 1) => {
    const response = await fetch(`${route('category.posts',category.id)}?page=${page}`);
    laravelData.value = await response.json();
}
getResults();
</script>

<template>
    <DefaultLayout>
    <Head title="Kategori" />
        <template v-slot:content>
            <!-- Posts Section -->
            <section class="w-full flex flex-col items-center px-3">
                <div class="grid grid-cols-3 gap-3 w-full">
                    <BlogPostPreview v-for="(post, index) in laravelData.data" :key="index"
                                     :id="post.id"
                                     :name="post.name"
                                     :category="post.category?.name ?? '#'"
                                     :description="post.content"
                                     :img="'/'+post.image"/>
                </div>
                <!-- Pagination -->
                <TailwindPagination
                    :data="laravelData"
                    @pagination-change-page="getResults"
                />

            </section>
        </template>

    </DefaultLayout>
</template>

<style>
</style>
