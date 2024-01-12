<script setup>
import {ref} from "vue";
import Placeholder from "@/App/Components/Placeholder.vue";

const loaded = ref(false);

const onImageLoad = () => {
    loaded.value = true;
};

const { id, name, category, description, img } = defineProps(
    ['id', 'name', 'category', 'description', 'img'],
    context
);
</script>
<template>
    <article class="flex flex-col w-full shadow my-4">
        <!-- Article Image -->
        <a :href="route('post.view',id)" class="hover:opacity-75">
            <Placeholder v-if="!loaded" height="300px" width="100%"></Placeholder>
            <img v-show="loaded" :src="img" @load="onImageLoad" class="blog-img">
        </a>
        <div class="bg-white flex flex-col justify-start p-6">
            <a :href="route('post.view',id)" class="text-blue-700 text-sm font-bold uppercase pb-4">{{category}}</a>
            <a :href="route('post.view',id)" class="text-3xl font-bold hover:text-gray-700 pb-4">{{name}}</a>
<!--            <p href="#" class="text-sm pb-3">
                By <a href="#" class="font-semibold hover:text-gray-800">David Grzyb</a>, Published on April 25th, 2020
            </p>-->
            <a :href="route('post.view',id)" class="pb-6">{{description}}</a>
            <a :href="route('post.view',id)" class="uppercase text-gray-800 hover:text-black">Görüntüle <i class="fas fa-arrow-right"></i></a>
        </div>
    </article>
</template>
<style scoped>
    @media (min-width: 992px){
        .blog-img{
            height: 300px;
            object-fit: cover;
        }
    }
</style>
