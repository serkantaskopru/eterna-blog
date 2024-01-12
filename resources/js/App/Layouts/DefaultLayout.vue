<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Placeholder from "@/App/Components/Placeholder.vue";

const showingNavigationDropdown = ref(false);
const fetchingCategories = ref(true);

const { appName, canLogin, canRegister} = defineProps(
    ['appName', 'canLogin', 'canRegister'],
    context
);
const categories = ref([]);
const fetchCategories = () => {
    axios.get(route('category.fetch'),{
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })
        .then(response => {
            categories.value = response.data;
            fetchingCategories.value = false;
        })
        .catch(error => {
            fetchingCategories.value = false;
        });
};
fetchCategories();
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">

            <!-- Main navigation container -->
            <nav class="w-full py-4 bg-blue-800 shadow">
                <div class="w-full container mx-auto flex flex-wrap items-center justify-between">
                    <nav v-if="!$page.props.auth.user">
                        <ul class="flex items-center justify-between font-bold text-sm text-white uppercase no-underline">
                            <li><a class="hover:text-gray-200 hover:underline px-4" :href="route('login')">Giriş Yap</a></li>
                            <li><a class="hover:text-gray-200 hover:underline px-4" :href="route('register')">Kayıt Ol</a></li>
                        </ul>
                    </nav>

                    <nav v-if="!canLogin && $page.props.auth.user">
                        <ul class="flex items-center justify-between font-bold text-sm text-white uppercase no-underline">
                            <li ><a class="hover:text-gray-200 hover:underline px-4" :href="route('dashboard')">Kullanıcı Paneli</a></li>
                        </ul>
                    </nav>

                    <div v-if="!canLogin && $page.props.auth.user" class="flex items-center text-lg no-underline text-white pr-6">
                        <Link
                            :href="route('logout')"
                            method="post" as="button"
                            class="hover:text-gray-200 hover:underline px-4"
                        >
                            Çıkış Yap
                        </Link>
                    </div>
                </div>

            </nav>

            <!-- Text Header -->
            <header class="w-full container mx-auto">
                <div class="flex flex-col items-center py-12">
                    <a class="font-bold text-gray-800 uppercase hover:text-gray-700 text-5xl" href="/">
                        TEST TEST
                    </a>
                </div>
            </header>

            <!-- Topic Nav -->
            <nav class="w-full py-4 border-t border-b bg-gray-100" x-data="{ open: false }">
                <div class="block sm:hidden">
                    <a
                        href="#"
                        class="block md:hidden text-base font-bold uppercase text-center flex justify-center items-center"
                        @click="open = !open"
                    >
                        Topics <i :class="open ? 'fa-chevron-down': 'fa-chevron-up'" class="fas ml-2"></i>
                    </a>
                </div>
                <div :class="open ? 'block': 'hidden'" class="w-full flex-grow sm:flex sm:items-center sm:w-auto">
                    <div class="w-full container mx-auto flex flex-col sm:flex-row items-center justify-center text-sm font-bold uppercase mt-0 px-6 py-2">
                        <Placeholder v-if="fetchingCategories" height="50px" width="100%"></Placeholder>
                        <a v-if="!fetchingCategories"
                           v-for="category in categories"
                           :key="category.id"
                           :href="route('category.view', category.id)"
                           class="hover:bg-gray-400 rounded py-2 px-4 mx-2">
                            {{ category.name }}
                        </a>

                    </div>
                </div>
            </nav>

            <div class="container mx-auto flex flex-wrap py-6">
                <slot name="content"/>
            </div>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->

            <footer class="w-full border-t bg-white pb-12">
                <div class="w-full container mx-auto flex flex-col items-center">
                    <div class="flex flex-col md:flex-row text-center md:text-left md:justify-between py-6">
                        <a href="#" class="uppercase px-3">About Us</a>
                        <a href="#" class="uppercase px-3">Privacy Policy</a>
                        <a href="#" class="uppercase px-3">Terms & Conditions</a>
                        <a href="#" class="uppercase px-3">Contact Us</a>
                    </div>
                    <div class="uppercase pb-6">&copy; myblog.com</div>
                </div>
            </footer>
        </div>
    </div>
</template>
