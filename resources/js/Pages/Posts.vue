<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm, router} from '@inertiajs/vue3';
import {nextTick, ref} from "vue";
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Editor from '@tinymce/tinymce-vue'
import InputError from '@/Components/InputError.vue';

const openPostCreateModal = ref(false);
const openPostUpdateModal = ref(false);
const openPostDeleteModal = ref(false);
const deletablePostId = ref(0);
const editablePost = ref([]);
const tinyApiKey = window.Laravel.config.tinyApiKey;
const form = useForm({
    name: '',
    category_id: '',
    content: '',
    image: '',
    newsletter_send: 0,
});

const { posts, categories } = defineProps(
    ['posts', 'categories'],
    context
);

const createNewPost = () => {
    const formData = new FormData();
    formData.append('name', form.name);
    formData.append('content', form.content);
    formData.append('category_id', form.category_id);
    formData.append('image', form.image);
    formData.append('newsletter_send', form.newsletter_send);

    axios.post(route('dashboard.post.create'), formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })
        .then(response => {
            closePostCreateModal();
            router.reload();
        })
        .catch(error => {
            console.error(error);
        });
};

const deletePost = () => {
    const formData = new FormData();
    formData.append('post_id', deletablePostId.value);

    axios.post(route('dashboard.post.delete'), formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })
        .then(response => {
            closePostDeleteModal();
            router.reload();
        })
        .catch(error => {
            console.error(error);
        });
};

const updatePost = (id) => {
    const post = editablePost.value;
    const formData = new FormData();
    formData.append('name', post.name);
    formData.append('content', post.content);
    formData.append('post_id', post.id);
    formData.append('category_id', post.category_id);
    formData.append('image', post.image);

    axios.post(route('dashboard.post.update'), formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })
        .then(response => {
            closePostUpdateModal();
            router.reload();
        })
        .catch(error => {
            console.error(error);
        });
};

const openPostUpdateModalDialog = (id) => {
    axios.get(route('dashboard.post.edit',id),{
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })
        .then(response => {
            editablePost.value = response.data;
            openPostUpdateModal.value = true;
        })
        .catch(error => {
        });
};
const openPostCreateModalDialog = () => {
    openPostCreateModal.value = true;
};
const openPostDeleteModalDialog = (id) => {
    deletablePostId.value = id;
    openPostDeleteModal.value = true;
};

const closePostCreateModal = () => {
    openPostCreateModal.value = false;
    form.reset();
};
const closePostUpdateModal = () => {
    openPostUpdateModal.value = false;
};
const closePostDeleteModal = () => {
    openPostDeleteModal.value = false;
};

const handleImageUpload = (event) => {
    form.image = event.target.files[0];
};
const handleUpdateImageUpload = (target, event) => {
    console.log(event)
    editablePost.value.image = event.target.files[0];
};
</script>

<template>
    <Head title="Posts"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Yazılar</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Start block -->
                    <section class="antialiased">
                        <div class="mx-auto max-w-screen-xl">
                            <!-- Start coding here -->
                            <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden">
                                <div
                                    class="flex flex-col md:flex-row items-center justify-end space-y-3 md:space-y-0 md:space-x-4 p-4">
                                    <div
                                        class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                                        <button @click="openPostCreateModalDialog" id="createProductModalButton"
                                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                            <i class="fas fa-plus mr-3"></i>
                                            Yazı Oluştur
                                        </button>
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left text-gray-500 ">
                                        <thead
                                            class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-4">Yazı Başlığı</th>
                                            <th scope="col" class="px-4 py-3">Kategori</th>
                                            <th scope="col" class="px-4 py-3">Paylaşım Tarihi</th>
                                            <th scope="col" class="px-4 py-3">İçeriği</th>
                                            <th scope="col" class="px-4 py-3">İşlemler</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Yazılar için döngü -->
                                        <tr v-for="(post, index) in posts" :key="index" class="border-b">
                                            <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                                {{ post.name }}
                                            </td>
                                            <td class="px-4 py-3">{{ post.category?.name ?? '#' }}</td>
                                            <td class="px-4 py-3">{{ post.created_at }}</td>
                                            <td class="px-4 py-3 max-w-[12rem] truncate">{{ post.content }}</td>
                                            <td class="flex items-center px-6 py-4">
                                                <a href="#" @click="openPostUpdateModalDialog(post.id)"
                                                   class="font-medium text-blue-600 hover:underline">Düzenle</a>
                                                <a href="#" @click="openPostDeleteModalDialog(post.id)"
                                                   class="font-medium text-red-600 hover:underline ms-3">Kaldır</a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- End block -->

                    <!-- Create modal -->
                    <Modal :show="openPostCreateModal" @close="closePostCreateModal" maxWidth="5xl">
                        <div class="p-6">
                            <h2 class="mb-6 text-lg font-medium text-gray-900">
                                Yeni bir blog gönderisi oluştur
                            </h2>
                            <form action="#" @submit.prevent="createNewPost" enctype="multipart/form-data">
                                <div class="grid gap-4 mb-4 sm:grid-cols-2">
                                    <div>
                                        <label
                                               class="block mb-2 text-sm font-medium text-gray-900">Başlık</label>
                                        <input type="text" name="name"
                                               v-model="form.name"
                                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 "
                                               placeholder="Yazı başlığını yazın" required="">
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900">Resim</label>
                                        <input type="file" name="image" v-on:change="handleImageUpload($event)" accept="image/*"
                                              class="block w-full text-sm text-gray-500
                                              border rounded-lg border-gray-300 file:me-4 file:py-2 file:px-4
                                              file:rounded rounded-lg file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-600 file:text-white
                                              hover:file:bg-blue-700
                                              file:disabled:opacity-50 file:disabled:pointer-events-none">
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900">Kategori</label>
                                        <select
                                            v-model="form.category_id"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 ">
                                            <option v-for="(category, index) in categories" :key="index" :value="category.id">{{category.name}}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900">Bülten</label>
                                        <input v-model="form.newsletter_send" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                        <label  class="ms-2 text-sm font-medium text-gray-900">Bülten olarak bildir</label>
                                    </div>
                                </div>
                                <Editor
                                    v-model="form.content"
                                    :api-key="tinyApiKey"
                                    :init="{
                                            plugins: 'lists link image table code help wordcount',
                                            content_style: 'body { width: 100% !important; }',
                                            width: '100%',
                                    }"
                                />
                            </form>
                            <div class="mt-6 flex justify-end">
                                <SecondaryButton @click="closePostCreateModal"> Vazgeç</SecondaryButton>

                                <DangerButton
                                    class="ms-3"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    @click="createNewPost"
                                >
                                    Yazıyı paylaş
                                </DangerButton>
                            </div>

                        </div>
                    </Modal>
                    <!-- Update modal -->
                    <Modal :show="openPostUpdateModal" @close="closePostUpdateModal" maxWidth="5xl">
                        <div class="p-6">
                            <h2 class="mb-6 text-lg font-medium text-gray-900">
                                {{editablePost.name}} yazısını düzenle
                            </h2>
                            <form action="#" @submit.prevent="updatePost(editablePost.id)" enctype="multipart/form-data">
                                <div class="grid gap-4 mb-4 sm:grid-cols-2">
                                    <div>
                                        <label
                                               class="block mb-2 text-sm font-medium text-gray-900">Başlık</label>
                                        <input type="text" name="name"
                                               v-model="editablePost.name"
                                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 "
                                               placeholder="Yazı başlığını yazın" required="">
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900">Resim</label>
                                        <input type="file" name="image" v-on:change="handleUpdateImageUpload(editablePost, $event)" accept="image/*"
                                               class="block w-full text-sm text-gray-500
                                              border rounded-lg border-gray-300 file:me-4 file:py-2 file:px-4
                                              file:rounded rounded-lg file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-600 file:text-white
                                              hover:file:bg-blue-700
                                              file:disabled:opacity-50 file:disabled:pointer-events-none">
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900">Kategori</label>
                                        <select
                                            v-model="editablePost.category_id"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 ">
                                            <option v-for="(category, index) in categories" :key="index" :value="category.id"
                                                    :selected="option === editablePost.category_id">{{category.name}}</option>
                                        </select>
                                    </div>

                                </div>
                                <Editor
                                    v-model="editablePost.content"
                                    :api-key="tinyApiKey"
                                    :init="{
                                            plugins: 'lists link image table code help wordcount',
                                            content_style: 'body { width: 100% !important; }',
                                            width: '100%',
                                    }"
                                />
                            </form>
                            <div class="mt-6 flex justify-end">
                                <SecondaryButton @click="closePostUpdateModal"> Vazgeç</SecondaryButton>

                                <DangerButton
                                    class="ms-3"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    @click="updatePost(editablePost.id)"
                                >
                                    Yazıyı düzenle
                                </DangerButton>
                            </div>

                        </div>
                    </Modal>
                    <!-- Delete modal -->
                    <Modal :show="openPostDeleteModal" @close="closePostDeleteModal" maxWidth="2xl">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900">
                                Yazıyı tamamen kaldırmak istediğinize emin misiniz?
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                Bu işlem geri alınamayacaktır.
                            </p>
                            <div class="mt-6 flex justify-end">
                                <SecondaryButton @click="closePostDeleteModal"> Vazgeç</SecondaryButton>

                                <DangerButton
                                    class="ms-3"
                                    @click="deletePost"
                                >
                                    Yazıyı Sil
                                </DangerButton>
                            </div>

                        </div>
                    </Modal>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<style scoped>
div.tox.tox-tinymce {
    width: 100% !important;
}
</style>
