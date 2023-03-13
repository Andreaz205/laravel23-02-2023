<template>
    <Spinner v-if="isLoading"/>
    <div class="modal fade" id="addModelModal" tabindex="-1" role="dialog" aria-labelledby="optionNameColorModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Редактировать свойство</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <Errors :errors="errors" />
                    <div class="form-group">
                        <label>Введите название(необязательно)</label>
                        <input type="text" class="form-control" v-model="title">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть
                    </button>
                    <button type="button" class="btn btn-primary bg-blue-500" @click="addModel">Добавить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <AuthenticatedLayout>
        <Link :href="`/admin/products/${product.id}`" class="text-blue-400 ml-4 my-1 btn btn-default">
            Назад к {{product.title}}
        </Link>
        <div class="card">
            <div class="card-header text-center text-xl bg-green">
                Прокручиваемые модели
            </div>
            <div class="card-body" v-if="modelsData && modelsData.length">

                <div class="row">
                    <div class="col-12">
                        <Errors :errors="errors" />
                    </div>
                </div>

                <div v-for="model in modelsData" class="border rounded-3xl mb-4">

                    <div class="row">
                        <div class="col-12 text-center text-lg flex justify-between items-center p-8">
                            <span>
                                  {{model.title ?? 'Модель'}}
                            </span>
                            <button class="btn btn-danger" @click="deleteModel(model)">
                                Удалить
                            </button>
                        </div>
                    </div>

                    <div class="row p-8" v-if="model.images && model.images.length">
                        <div class="col-12 flex flex-wrap gap-4" v-if="model.images && model.images.length">
                            <div v-for="image in model.images" class="h-[200px] w-[200px] relative">
                                <img :src="image.image_url" alt="" class="w-full h-full">
                                <div class="absolute top-0 right-0 bg-gray-400 w-[20px] h-[20px] flex justify-center items-center cursor-pointer" @click="deleteImage(image, model)">X</div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="h-[300px] flex justify-center items-center text-lg">
                        Здесь должны расположиться 32 фотографии для корректного отображения модели
                    </div>

                    <div>
                        <div :class="['bg-gray-500 h-[300px] text-white flex justify-center items-center m-8 cursor-pointer', model.images.length >= 32 && 'hidden']" :ref="`dropzone-${model.id}`">
                            Поместите здесь изображение либо кликните чтобы добавить
                        </div>
                    </div>
                    <div class="text-center">
                        {{model.images.length}} / 32
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button
                    class="btn btn-primary bg-blue"
                    type="button"
                    data-toggle="modal"
                    data-target="#addModelModal"
                >
                    Добавить
                </button>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Errors from "@/Components/Errors/Errors.vue";
import Spinner from "@/Components/Spinner.vue";
import Dropzone from "dropzone";
import {Link} from '@inertiajs/vue3'
export default {
    name: "ProductModels",
    components: {Spinner, Errors, AuthenticatedLayout, Link},
    props: ['modelsProp', 'product'],
    data () {
        return {
            errors: null,
            isLoading: false,
            modelsData: this.modelsProp,
            dropzones: this.modelsProp?.map(model => model.id) ?? [],
            title: '',
        }
    },
    methods: {
        async addModel() {
            try {
                this.isLoading = true
                let data = {
                    title: this.title
                }
                let response = await axios.post(`/admin/products/${this.product.id}/models`, data)
                this.modelsData.push(response.data)
                setTimeout(() => {
                    let ref = 'dropzone-' + response.data.id
                    this.initDropzone(ref, response.data)
                })
                this.errors = null
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) {
                    return this.errors = e.response.data.errors
                }
                alert(e?.message ?? e)
            }
        },
        async deleteModel(model) {
            try {
                await axios.delete(`/admin/products/${this.product.id}/models/${model.id}`)
                let index = this.modelsData.indexOf(model)
                this.modelsData.splice(index, 1)
            } catch (e) {
                alert(e?.message ?? e)
            }
        },
        async handleAddFile(modelId, file) {
            try {
                this.isLoading = true
                let formData = new FormData()
                formData.append('image', file)
                let response = await axios.post(`/admin/products/${this.product.id}/models/${modelId}/images`, formData)
                let newImage = response.data
                let searchedModel = this.modelsData.find(m => m.id === modelId)
                searchedModel.images.push(newImage)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                alert(e?.message ?? e)
            }
        },
        async deleteImage(image, model) {
            try {
                this.isLoading = true
                await axios.delete(`/admin/products/${this.product.id}/models/${model.id}/images/${image.id}`)
                let index = model.images.indexOf(image)
                model.images.splice(index, 1)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                alert(e?.message ?? e)
            }
        },
        initDropzone(ref, model) {
            let obj = new Dropzone(this.$refs[ref][0], {
                url: '/admin/products/image/test',
                autoProcessQueue: false,
                maxFiles: 10,
                disablePreviews: true
            })
            obj.on("addedfile", (file) => {
                this.handleAddFile(model.id, file)
            });
        }
    },
    watch: {
        modelsData(currentValue, previousValue) {
            // currentValue.map(model => {
            //     console.log(model)
            // })
        }
    },
    mounted() {
        this.dropzones.map(dropzone => {
            // console.log(this.dropzones)
            // console.log(this.$refs[`dropzone-${dropzone}`][0])
            // console.log(`dropzone-${dropzone}`)
            let obj = new Dropzone(this.$refs[`dropzone-${dropzone}`][0], {
                url: '/admin/products/image/test',
                autoProcessQueue: false,
                maxFiles: 10,
                disablePreviews: true
            })
            obj.on("addedfile", (file) => {
                this.handleAddFile(dropzone, file)
            });
        })
    }
}
</script>

<style scoped>

</style>
