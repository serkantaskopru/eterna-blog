<script setup>
import {ref} from "vue";
import { Head, Link } from '@inertiajs/vue3';
import DefaultLayout from "@/App/Layouts/DefaultLayout.vue";
import Placeholder from "@/App/Components/Placeholder.vue";

const loaded = ref(false);

const onImageLoad = () => {
    loaded.value = true;
};
const { post } = defineProps(
    ['post'],
    context
);
</script>

<template>
    <DefaultLayout>
    <Head title="Welcome" />
        <template v-slot:content>
            <!-- Post Section -->
            <section class="w-full flex flex-col items-center px-3">

                <article class="flex flex-col w-full shadow my-4">
                    <!-- Article Image -->
                    <a href="#" class="hover:opacity-75">
                        <Placeholder v-if="!loaded" height="500px" width="100%"></Placeholder>
                        <img v-show="loaded" :src="'/'+post.image" @load="onImageLoad">
                    </a>
                    <div class="bg-white flex flex-col justify-start p-6">
                        <a href="#" class="text-blue-700 text-sm font-bold uppercase pb-4">{{post.category?.name ?? '#'}}</a>
                        <a href="#" class="text-3xl font-bold hover:text-gray-700 pb-4">{{post.name}}</a>
                        <p href="#" class="text-sm pb-8">
                            Tarih: {{post.created_at}}
                        </p>
                        <div class="content-body" v-html="post.content"></div>
                    </div>
                </article>
            </section>
        </template>

    </DefaultLayout>
</template>

<style>
</style>
