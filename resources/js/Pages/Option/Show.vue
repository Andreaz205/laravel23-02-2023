<template>
    <AuthenticatedLayout>
        <div class="card">
            <div class="card-header">
                <div class="flex items-center ">
                    <button class="btn btn-default">
                        <Link href="/admin/options">
                            К свойствам
                        </Link>
                    </button>
                    <div class="ml-2">
                        Редактировать значения свойства <strong>{{optionName.title}}</strong>
                    </div>
                </div>

            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Фото</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-if="optionValues && optionValues.length">
                        <tr v-for="value in optionValues">
                            <td>{{value.id}}</td>
                            <td>{{value.title}}</td>
                            <td>
                                <template v-if="value.image">
                                    <div class="w-full h-full flex">
                                        <img
                                            class="h-[50px] w-[50px]"
                                            :src="value.image.image_url"
                                            alt=""
                                        >
                                        <div class="ml-2">
                                            <label :for="'update-image-' + value.id" class="btn btn-default">
                                                Заменить фото
                                            </label>
                                            <input type="file" :id="'update-image-' + value.id" @change="updatePhoto($event, value)" class="hidden">

                                        </div>
                                        <div class="ml-2">
                                            <button class="btn btn-danger" @click="deleteImage(value)">
                                                Удалить
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <div
                                    v-else
                                    class="h-[50px] w-[50px]"
                                >
                                    <label
                                        :for="'image-input-' + value.id"
                                        class="w-full h-full cursor-pointer"
                                        title="Добавить фото"
                                    >
                                        <img
                                            class="w-full h-full"
                                            src="/storage/images/no-image.jpg"
                                        >
                                    </label>
                                    <input type="file" :id="'image-input-' + value.id" class="hidden" @change="handleImageUpload($event, value)">
                                </div>

                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">

            </div>
        </div>
    </AuthenticatedLayout>

</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {Link} from '@inertiajs/vue3'
export default {
    name: "Show",
    components: {AuthenticatedLayout, Link},
    props: [
        'optionName',
        'optionValues'
    ],
    data () {
        return {

        }
    },
    methods: {
        async handleImageUpload(event, value) {
            try {
                let file = event.target.files[0]
                let formData = new FormData()
                formData.append('image', file)
                let response = await axios.post(`/admin/options/option-values/${value.id}/image`, formData)
                let newImage = response.data.data
                value.image = newImage
            } catch (e) {
                alert(e?.message ?? e)
            }
        },
        async updatePhoto(event, value) {
            try {
                let file = event.target.files[0]
                let formData = new FormData()
                formData.append('image', file)
                let response = await axios.post(`/admin/options/option-values/${value.id}/image/update`, formData)
                let newImage = response.data.data
                value.image = newImage
            } catch (e) {
                alert(e?.message ?? e)
            }
        },
        async deleteImage(value) {
            try {
                await axios.delete(`/admin/options/option-values/${value.id}/image`)
                value.image = null
            } catch (e) {
                alert(e?.message ?? e)
            }
        }
    }

}
</script>

<style scoped>

</style>
