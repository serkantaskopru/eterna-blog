<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm, router} from '@inertiajs/vue3';
import {nextTick, ref} from "vue";
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Editor from '@tinymce/tinymce-vue'
import InputError from '@/Components/InputError.vue';

const openCategoryCreateModal = ref(false);
const openCategoryUpdateModal = ref(false);
const openCategoryDeleteModal = ref(false);
const deletableCategoryId = ref(0);
const editableCategory = ref([]);
const form = useForm({
    name: '',
});

const { categories } = defineProps(
    ['categories'],
    context
);

const createNewCategory = () => {
    const formData = new FormData();
    formData.append('name', form.name);

    axios.post(route('dashboard.category.create'), formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })
        .then(response => {
            closeCategoryCreateModal();
            router.reload();
        })
        .catch(error => {
            console.error(error);
        });
};

const deleteCategory = () => {
    const formData = new FormData();
    formData.append('category_id', deletableCategoryId.value);

    axios.post(route('dashboard.category.delete'), formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })
        .then(response => {
            closeCategoryDeleteModal();
            router.reload();
        })
        .catch(error => {
            console.error(error);
        });
};

const updateCategory = (id) => {
    const category = editableCategory.value;
    const formData = new FormData();
    formData.append('name', category.name);
    formData.append('category_id', id);

    axios.post(route('dashboard.category.update'), formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })
        .then(response => {
            closeCategoryUpdateModal();
            router.reload();
        })
        .catch(error => {
            console.error(error);
        });
};

const openCategoryUpdateModalDialog = (id) => {
    axios.get(route('dashboard.category.edit',id),{
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })
        .then(response => {
            editableCategory.value = response.data;
            openCategoryUpdateModal.value = true;
        })
        .catch(error => {
        });
};
const openCategoryCreateModalDialog = () => {
    openCategoryCreateModal.value = true;
};
const openCategoryDeleteModalDialog = (id) => {
    deletableCategoryId.value = id;
    openCategoryDeleteModal.value = true;
};

const closeCategoryCreateModal = () => {
    openCategoryCreateModal.value = false;
    form.reset();
};
const closeCategoryUpdateModal = () => {
    openCategoryUpdateModal.value = false;
};
const closeCategoryDeleteModal = () => {
    openCategoryDeleteModal.value = false;
};

const handleImageUpload = (target, event) => {
    form.image = event.target.files[0];
};
const handleUpdateImageUpload = (target, event) => {
    console.log(event)
    editableCategory.value.image = event.target.files[0];
};
</script>

<template>
    <Head title="Kategoriler"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kategoriler</h2>
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
                                        <button @click="openCategoryCreateModalDialog" id="createProductModalButton"
                                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                            <i class="fas fa-plus mr-3"></i>
                                            Kategori Oluştur
                                        </button>
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left text-gray-500 ">
                                        <thead
                                            class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-4">Kategori Başlığı</th>
                                            <th scope="col" class="px-4 py-3 text-end">İşlemler</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Kategoriler için döngü -->
                                        <tr v-for="(category, index) in categories" :key="index" class="border-b">
                                            <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                                {{ category.name }}
                                            </td>
                                            <td class="flex items-center justify-end px-6 py-4">
                                                <a href="#" @click="openCategoryUpdateModalDialog(category.id)"
                                                   class="font-medium text-blue-600 hover:underline">Düzenle</a>
                                                <a href="#" @click="openCategoryDeleteModalDialog(category.id)"
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
                    <Modal :show="openCategoryCreateModal" @close="closeCategoryCreateModal" maxWidth="5xl">
                        <div class="p-6">
                            <h2 class="mb-6 text-lg font-medium text-gray-900">
                                Yeni kategori oluştur
                            </h2>
                            <form action="#" @submit.prevent="createNewCategory" enctype="multipart/form-data">
                                <div class="grid gap-4 mb-4 sm:grid-cols-2">
                                    <div>
                                        <label
                                               class="block mb-2 text-sm font-medium text-gray-900">Başlık</label>
                                        <input type="text" name="name"
                                               v-model="form.name"
                                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 "
                                               placeholder="Kategori başlığını yazın" required="">
                                    </div>
                                </div>
                            </form>
                            <div class="mt-6 flex justify-end">
                                <SecondaryButton @click="closeCategoryCreateModal"> Vazgeç</SecondaryButton>

                                <DangerButton
                                    class="ms-3"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    @click="createNewCategory"
                                >
                                    Oluştur
                                </DangerButton>
                            </div>

                        </div>
                    </Modal>
                    <!-- Update modal -->
                    <Modal :show="openCategoryUpdateModal" @close="closeCategoryUpdateModal" maxWidth="5xl">
                        <div class="p-6">
                            <h2 class="mb-6 text-lg font-medium text-gray-900">
                                {{editableCategory.name}} kategorisini düzenle
                            </h2>
                            <form action="#" @submit.prevent="updateCategory(editableCategory.id)" enctype="multipart/form-data">
                                <div class="grid gap-4 mb-4 sm:grid-cols-2">
                                    <div>
                                        <label
                                               class="block mb-2 text-sm font-medium text-gray-900">Başlık</label>
                                        <input type="text" name="name"
                                               v-model="editableCategory.name"
                                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 "
                                               placeholder="Yazı başlığını yazın" required="">
                                    </div>
                                    </div>
                            </form>
                            <div class="mt-6 flex justify-end">
                                <SecondaryButton @click="closeCategoryUpdateModal"> Vazgeç</SecondaryButton>

                                <DangerButton
                                    class="ms-3"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    @click="updateCategory(editableCategory.id)"
                                >
                                    Kategoriyi düzenle
                                </DangerButton>
                            </div>

                        </div>
                    </Modal>
                    <!-- Delete modal -->
                    <Modal :show="openCategoryDeleteModal" @close="closeCategoryDeleteModal" maxWidth="2xl">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900">
                                Yazıyı tamamen kaldırmak istediğinize emin misiniz?
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                Bu işlem geri alınamayacaktır.
                            </p>
                            <div class="mt-6 flex justify-end">
                                <SecondaryButton @click="closeCategoryDeleteModal"> Vazgeç</SecondaryButton>

                                <DangerButton
                                    class="ms-3"
                                    @click="deleteCategory"
                                >
                                    Kategoriyi Sil
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
