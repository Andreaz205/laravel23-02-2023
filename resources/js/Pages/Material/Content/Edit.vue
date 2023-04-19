<template>
    <Spinner v-if="isLoading" />

    <!--    TODO: Модальное окно для создания элементов с изобрнажениями для контента-->
    <div class="modal fade" id="appendItemModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Выберите категорию</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <Errors :errors="errors" />
                    <div class="row">
                        <label class="col-3">Описание</label>
                        <input type="text" class="col-9 form-control" v-model="itemDescription">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" ref="closeCategoriesModal" data-dismiss="modal">Закрыть
                    </button>
                    <button class="btn btn-primary" @click="appendItem">
                        Добавить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--    TODO: Модальное окно для создания текстовых элементов для контента-->
    <div class="modal fade" id="appendTextItemModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Выберите категорию</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <Errors :errors="errors" />
                    <div class="row">
                        <label class="col-3">Заголовок</label>
                        <input type="text" class="col-9 form-control" v-model="textItemTitle">
                    </div>

                    <div class="row mt-2">
                        <label class="col-3">Описание</label>
                        <input type="text" class="col-9 form-control" v-model="textItemDescription">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" ref="closeCategoriesModal" data-dismiss="modal">Закрыть
                    </button>
                    <button class="btn btn-primary" @click="appendTextItem">
                        Добавить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header text-center text-lg relative">
            Редактировать контент {{variantContent.title}}
            <button class="btn btn-default absolute top-1/2 -translate-y-1/2 left-1">
                <Link href="/admin/materials">
                    К материалам
                </Link>
            </button>
        </div>
    </div>

    <div class="card m-3">
        <div class="card-body">
            <div class="row">
                <div class="col-12 text-center">
                    Элементы с изображением:
                </div>

                <template v-if="variantContent.items && variantContent.items.length">
                    <div v-for="item in variantContent.items" class="col-12">
                        <div class="flex justify-between">
                            <img v-if="item.image_url" :src="item.image_url" alt="" class="w-[100px] h-[100px]">

                            <button v-else class="btn btn-default">
                                <label :for="`image-button-${item.id}`" class="m-0 w-full h-full cursor-pointer">
                                    Добавить фото
                                </label>
                                <input type="file" class="hidden" :id="`image-button-${item.id}`" @change="appendImage($event, item)">
                            </button>
                            -
                            <div>
                                {{item.description}}
                                <button class="btn btn-danger" @click="deleteItem(item)">
                                    Х
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="col-12 text-center">
                    <button
                        class="btn btn-primary bg-blue"
                        data-toggle="modal"
                        data-target="#appendItemModal"
                    >
                        Добавить
                    </button>

                </div>
            </div>
        </div>
    </div>

    <div class="card m-3">
        <div class="card-body">
            <div class="row">
                <div class="col-12 text-center">
                    Текстовые элементы:
                </div>

                <template v-if="variantContent.text_items && variantContent.text_items.length">
                    <div v-for="item in variantContent.text_items" class="col-12">
                        <div class="flex justify-between">
                            <div>
                                {{item.title}}
                            </div>
                            -
                            <div>
                                {{item.description}}
                                <button class="btn btn-danger" @click="deleteTextItem(item)">
                                    Х
                                </button>
                            </div>
                        </div>

                    </div>
                </template>

                <div class="col-12 text-center">
                    <button
                        class="btn btn-primary bg-blue"
                        data-toggle="modal"
                        data-target="#appendTextItemModal"
                    >
                        Добавить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card m-3">
        <div class="card-body">
            <div class="row">
                <div class="col-12 text-center text-lg">
                    Указанные значения
                </div>

                <div class="col-12">
                    <template v-for="value in this.variantContent.material_unit_values">
                        {{value.value}} ,
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {Link} from '@inertiajs/vue3'
import Spinner from "@/Components/Spinner.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Errors from "@/Components/Errors/Errors.vue";

export default {
    components: {Errors, Spinner, Link},
    name: "Edit",
    layout: AuthenticatedLayout,
    props: [
        'variantContent'
    ],
    data(){
        return {
            errors: null,
            isLoading: false,
            itemDescription: null,
            textItemDescription: null,
            textItemTitle: null,
        }
    },
    methods: {
        async appendItem() {
            try {
                this.isLoading = true
                let data = {
                    description: this.itemDescription
                }
                let response = await axios.post(`/admin/materials/variants-content/${this.variantContent.id}/append-item`, data)
                if (this.variantContent.items) this.variantContent.items.push(response.data)
                else this.variantContent.items = [response.data]
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                if (e?.response?.status === 500) return this.errors = [e.response.message]
                alert(e.message ?? e)
            }
        },
        async deleteItem(item) {
            try {
                this.isLoading = true
                await axios.delete(`/admin/materials/variants-content-items/${item.id}`)
                let index = this.variantContent.items.indexOf(item)
                this.variantContent.items.splice(index, 1)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                if (e?.response?.status === 500) return this.errors = [e.response.message]
                alert(e.message ?? e)
            }
        },
        async appendTextItem() {
            try {
                this.isLoading = true
                let data = {
                    title: this.textItemTitle,
                    description: this.textItemDescription,
                }
                let response = await axios.post(`/admin/materials/variants-content/${this.variantContent.id}/append-text-item`, data)
                if (this.variantContent.text_items) this.variantContent.text_items.push(response.data)
                else this.variantContent.text_items = [response.data]
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                if (e?.response?.status === 500) return this.errors = [e.response.message]
                alert(e.message ?? e)
            }
        },
        async deleteTextItem(item) {
            try {
                this.isLoading = true
                await axios.delete(`/admin/materials/variants-content-text-items/${item.id}`)
                let index = this.variantContent.text_items.indexOf(item)
                this.variantContent.text_items.splice(index, 1)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                if (e?.response?.status === 500) return this.errors = [e.response.message]
                alert(e.message ?? e)
            }
        },
        async appendImage(event, item) {
            try {
                this.isLoading = true
                let file = event.target.files[0]
                let formData = new FormData
                formData.append('image', file)
                let response = await axios.post(`/admin/materials/variants-content-items/${item.id}/append-image`, formData)
                item.image_url = response.data.image_url
                item.image_path = response.data.image_path
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                if (e?.response?.status === 500) return this.errors = [e.response.message]
                alert(e.message ?? e)
            }
        }
    }
}
</script>

<style scoped>

</style>
