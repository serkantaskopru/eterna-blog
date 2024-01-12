<script setup>
import {Head, Link, router, useForm} from '@inertiajs/vue3';
import DefaultLayout from "@/App/Layouts/DefaultLayout.vue";
import BlogPostPreview from "@/App/Components/BlogPostPreview.vue";
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    email: '',
});

const { posts, canLogin, canRegister, laravelVersion, phpVersion, appName } = defineProps(
    ['posts', 'canLogin', 'canRegister', 'laravelVersion', 'phpVersion', 'appName'],
    context
);

const submitNewsletterForm = () => {
    form.post(route('newsletter.store'), {
        onFinish: () => {
            form.reset();
            alert('Bülten aboneliğiniz başarıyla oluşturuldu')
        },
    });
};

</script>

<template>
    <DefaultLayout>
    <Head title="Anasayfa"/>
        <template v-slot:content>

            <!-- Posts Section -->
            <section class="w-full md:w-2/3 flex flex-col items-center px-3">
                <div class="grid grid-cols-2 gap-3">
                <BlogPostPreview v-for="(post, index) in posts" :key="index"
                    :id="post.id"
                    :name="post.name"
                    :category="post.category?.name ?? '#'"
                    :description="post.content"
                    :img="post.image"/>
                </div>
            </section>

            <!-- Sidebar Section -->
            <aside class="w-full md:w-1/3 flex flex-col items-center px-3">

                <div class="w-full bg-white shadow flex flex-col my-4 p-6">
                    <p class="text-xl font-semibold pb-5">Bülten</p>
                    <form @submit.prevent="submitNewsletterForm">
                        <div>
                            <InputLabel for="email" value="Email" />

                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                required
                                autofocus
                                placeholder="E-posta adresinizi yazın"
                            />

                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>
                        <div class="flex items-center justify-end mt-2">
                            <button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="w-full bg-blue-800 text-white font-bold text-sm uppercase rounded hover:bg-blue-700 flex items-center justify-center px-2 py-3 mt-4">
                                BÜLTENE KAYIT OL
                            </button>
                        </div>
                    </form>
                </div>

            </aside>
        </template>

    </DefaultLayout>
</template>

<style>
</style>
