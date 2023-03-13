<template>
    <Spinner v-if="isLoading"/>
    <div class="modal fade" id="addProductsModal" tabindex="-1" role="dialog" aria-labelledby="optionNameColorModal"
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
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div v-if="products" class="border">
                                    <div v-for="product in products" class="d-flex justify-between items-center border-b">
                                        <div>
                                            {{product.title}}
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" :id="'custom-switch' + product.id" :checked="product.is_exists" @change="addProduct(product)">
                                                <label class="custom-control-label" :for="'custom-switch' + product.id" style="cursor: pointer;">Добавить в акцию</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
    <AuthenticatedLayout>
        <div>
            <Link class="btn btn-default ml-4 my-1" href="/admin/sales">
                Назад
            </Link>
        </div>


        <div class="card">
            <div class="card-header text-center text-xl">
                {{sale.title}}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <Errors :errors="errors" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <label>
                            Изображение
                        </label>
                    </div>
                    <div class="col-8">
                        <div class="h-[400px] w-[400px] bg-gray-50 rounded-xl overflow-hidden relative" v-if="sale.image_url">
                            <img :src="sale.image_url" alt="" class="object-contain w-full h-full">

                            <div class="absolute flex justify-between items-center top-0 w-full p-2">
                                <input type="file" id="image-for-change" @change="storeImage($event.target.files[0])" class="hidden">
                                <button class="btn btn-default">
                                    <label for="image-for-change" class="cursor-pointer mb-0">Заменить изображение</label>
                                </button>
                                <button class="btn btn-danger" @click="deleteImage">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                        </div>
                        <div ref="dropzone" :class="['h-[400px] w-[400px] bg-gray overflow-hidden flex justify-center items-center cursor-pointer rounded-xl overflow-hidden', sale.image_url && 'hidden']">
                            Поместите изображение либо нажмите
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-4">
                        <label>
                            Товары:
                        </label>
                    </div>
                    <template v-if="sale.products && sale.products.length">
                        <div class="col-6">
                            <span v-for="(product, key) in sale.products.slice(0, 5)" :key="key">
                                {{product.title}}
                                <span v-if="key !== 4">, </span>
                            </span>
                            <span v-if="sale.products.length > 5" class="text-bold">
                                + {{sale.products.length - 5}} товаров
                            </span>
                        </div>
                        <div class="col-2">
                            <button
                                class="btn btn-warning bg-yellow"
                                type="button"
                                data-toggle="modal"
                                data-target="#addProductsModal"
                                @click="fetchProducts"
                            >
                                Редактировать
                            </button>
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-8">
                            <button
                                class="btn btn-warning bg-yellow"
                                type="button"
                                data-toggle="modal"
                                data-target="#addProductsModal"
                                @click="fetchProducts"
                            >
                                Редактировать
                            </button>
                        </div>
                    </template>

                </div>

                <div class="row mt-4">
                    <div class="col-4">
                        <label>
                            Опубликовать:
                        </label>
                    </div>
                    <div class="col-8">
                        <CustomSwitch :is-checked="data.is_public" @changeSwitch="handlePublicSwitch" switch-id="public-switch"/>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button class="btn btn-danger" @click="deleteSale">
                    Удалить
                </button>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Link} from "@inertiajs/vue3";
import Spinner from "@/Components/Spinner.vue";
import Errors from "@/Components/Errors/Errors.vue";
import Dropzone from "dropzone";
import CustomSwitch from "@/Components/CustomSwitch.vue";
export default {
    name: "Sale",
    components: {CustomSwitch, Errors, Spinner, AuthenticatedLayout, Link},
    props: ['data'],
    data () {
        return {
            errors: null,
            isLoading: false,
            isVariantsOpen: false,
            sale: this.$props.data,
            image: null,
            products: null,
            dropzone: null,
            file: null,
        }
    },
    methods: {
        async handlePublicSwitch (e) {
            try {
                this.isLoading = true
                let {data} = await axios.post(`/admin/sales/${this.sale.id}/public`)
                this.isLoading = false
                // this.data.is_publi
            } catch (e) {
                this.isLoading = false
                alert(e.message ?? e)
            }
        },
        async addProduct(product) {
            try {
                let {data} = await axios.get(`/admin/sales/${this.sale.id}/toggle-exists-product/${product.id}`)
                let candidate = this.sale.products.find(p => p.id === product.id)
                if (candidate) {
                    let index = this.sale.products.indexOf(candidate)
                    this.sale.products.splice(index, 1)
                } else {
                    let clone = JSON.parse(JSON.stringify(product))
                    this.sale.products.push(clone)
                }
                // if (!candidate) {
                //     if (this.sale.products_count > 5) {
                //         console.log(1)
                //         ++this.sale.products_count
                //     } else {
                //         console.log(2)
                //         let newProduct = JSON.parse(JSON.stringify(product))
                //         this.sale.products.push(newProduct)
                //         ++this.sale.products_count
                //     }
                //
                // } else {
                //     this.products.map(prod => {
                //         if (prod.id === product.id) {
                //             prod.is_exists = !prod.is_exists
                //             if (prod.is_exists && this.sale.products_count <= 5) {
                //                 console.log(3)
                //                 let searchedProduct = this.sale.products?.find(p => p.id === prod.id)
                //                 let index = this.sale.products.indexOf(searchedProduct)
                //                 console.log(index)
                //                 this.sale.products.splice(index, 1)
                //                 --this.sale.products_count
                //             } else {
                //                 console.log(4)
                //                 --this.sale.products_count
                //             }
                //         }
                //     })
                // }

            } catch (e) {
                alert(e.message ?? e)
            }
        },
        async fetchProducts() {
            try {
                this.isLoading = true
                let response = await axios.get(`/admin/sales/${this.sale.id}/sale-products`)
                this.products = response.data
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.data?.errors) return this.errors = e.response.data.errors
                alert(e.message ?? e)
            }

        },
        async storeImage(file) {
            try {
                this.isLoading = true
                let formData = new FormData()
                formData.append('image', file)
                let {data} = await axios.post(`/admin/sales/${this.sale.id}/image`, formData)
                this.sale.image_url = data.image_url
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                alert(e.message ?? e)
            }
        },
        async deleteImage(){
            try {
                this.isLoading = true
                let {data} = await axios.delete(`/admin/sales/${this.sale.id}/image`)
                this.sale.image_url = null
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                alert(e.message ?? e)
            }
        },
        async deleteSale() {
            try {
                this.isLoading = true
                await this.$inertia.delete(`/admin/sales/${this.data.id}`)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.data?.errors) return this.errors = e.response.data.errors
                alert(e.message ?? e)
            }
        }
    },
    computed: {},
    mounted() {
        this.dropzone = new Dropzone(this.$refs.dropzone, {
            url: '/',
            autoProcessQueue: false,
            maxFiles: 10,
            disablePreviews: true
        })
        this.dropzone.on("addedfile", async (file) => this.storeImage(file))
    }
}
</script>

<style scoped lang="scss">
.sale-image{
    width: 200px;
    height: 200px;
}
</style>
