<template>

    <div class="modal fade" id="createSizeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить товар</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <Errors :errors="errors" />
                    <div class="row">
                        <label class="col-4">Название<span class="text-red">*</span></label>
                        <input class="form-control col-8" v-model="newSizeTitle">
                    </div>

                    <div class="row mt-2">
                        <label class="col-4">Длина<span class="text-red">*</span></label>
                        <input class="form-control col-8" v-model="newSizeLength" >
                    </div>

                    <div class="row mt-2">
                        <label class="col-4">Ширина</label>
                        <input class="form-control col-8" v-model="newSizeWidth" >
                    </div>

                    <div class="row mt-2">
                        <label class="col-4">Высота</label>
                        <input class="form-control col-8" v-model="newSizeHeight" >
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary bg-blue-500" @click="appendSize">Добавить</button>
                </div>
            </div>
        </div>
    </div>
    <AuthenticatedLayout>
        <div class="card">
            <div class="card-header text-center text-lg relative">
                Редактировать информацию о товаре
                <button class="absolute btn btn-default top-1/2 -translate-y-1/2 left-1">
                    <Link :href="`/admin/products/${product.id}`">
                        К {{product.title}}
                    </Link>
                </button>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                <div class="row">

                    <div class="col-4">
                        <label>Длина</label>
                        <input type="text" class="form-control" v-model="length" placeholder="Введите длину см">
                    </div>
                    <div class="col-4">
                        <label>Ширина</label>
                        <input type="text" class="form-control" v-model="width" placeholder="Введите ширину см">
                    </div>
                    <div class="col-4">
                        <label>Высота</label>
                        <input type="text" class="form-control" v-model="height" placeholder="Введите высоту см">
                    </div>

                </div>

                <template v-if="additional_sizes">
                    <div class="row mt-3" v-for="size in additional_sizes">
                        <div class="relative col-12 text-center text-lg">
                            {{size.title}}
                            <button class="btn btn-danger absolute top-0 right-0" @click="deleteSize(size)">
                                x
                            </button>
                        </div>

                        <div class="col-12">
                            <div class="row">
                                <div class="col-4">
                                    <label>Длина</label>
                                    <input type="text" class="form-control" :value="size.length"  disabled>
                                </div>

                                <div class="col-4">
                                    <label>Ширина</label>
                                    <input type="text" class="form-control" :value="size.width" disabled>
                                </div>

                                <div class="col-4">
                                    <label>Высота</label>
                                    <input type="text" class="form-control" :value="size.height" disabled>
                                </div>
                            </div>
                        </div>

                    </div>
                </template>


                <div class="row">
                    <div class="col-12 text-center mt-3">
                        <button
                                type="button"
                                class="btn btn-primary bg-blue-500"
                                data-toggle="modal"
                                data-target="#createSizeModal"
                        >
                            Добавить размер
                        </button>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <div class="row">
                        <label class="col-4">Вес</label>
                        <input type="text" v-model="weight" class="form-control col-8">
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-12">
                        <div class="form-group">
                            <label>Описание</label>
                            <textarea class="form-control" rows="3" v-model="description" placeholder="Введите описание"></textarea>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <span class="text-xl">Параметры</span>
                </div>

                <template v-if="parameters && parameters.length">

                    <div class="row" v-for="parameter in parameters">
                        <div class="col-5">
                            <div class="form-group">
                                <label>Название</label>
                                <input type="text" class="form-control" v-model="parameter.title" placeholder="Введите название для параметра">
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>Значение</label>
                                <input type="text" class="form-control" v-model="parameter.value" placeholder="Введите значение для параметра">
                            </div>
                        </div>
                        <div class="col-2">
                            <button class="btn btn-danger" @click="deleteParameter(parameter)">
                                Удалить
                            </button>
                        </div>
                    </div>

                </template>


                    <div class="text-center mt-2">
                        <button class="btn btn-default" @click="addParameter">
                            Добавить параметры
                        </button>
                    </div>


                <div class="row">
                    <div class="col-12">
                        <div class="flex">
                            <CustomSwitch switch-id="allow-sales-switch" :is-checked="allow_sales" @changeSwitch="changeAllowSales"/>
                            <span>Применять скидки</span>
                        </div>
                    </div>
                </div>

<!--                <div class="form-group">-->
<!--                    <label>Textarea</label>-->
<!--                    <textarea class="form-control" rows="3" placeholder="Enter ..."></textarea>-->
<!--                </div>-->
                <button class="btn btn-primary" @click="onSubmit">
                    Сохранить
                </button>
            </div>
            <!-- /.card-body -->
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import CustomSwitch from "@/Components/CustomSwitch.vue";
import {router} from "@inertiajs/vue3";
import Errors from "@/Components/Errors/Errors.vue";
import {Link} from '@inertiajs/vue3'

export default {
    name: "Edit",
    components: {Errors, CustomSwitch, AuthenticatedLayout, Link},
    props: ['productData'],
    data () {
        return {
            newSizeLength: null,
            newSizeWidth: null,
            newSizeHeight: null,
            newSizeTitle: null,

            isLoading: false,
            errors: null,
            weight: this.productData.weight,
            product: this.productData,
            parameters: this.productData.parameters,
            width: this.productData.width,
            height: this.productData.height,
            length: this.productData.length,
            allow_sales: this.productData.allow_sales,
            description: this.productData.description,
            additional_sizes: JSON.parse(JSON.stringify(this.productData.additional_sizes)),
        }
    },
    methods: {
         changeAllowSales(event) {
            this.allow_sales = event.target.checked
        },
        addParameter() {
             let newLastId = this.parameters && this.parameters.length ? this.parameters[this.parameters.length - 1].id + 1 : 0
             this.parameters.push({
                 id: newLastId,
                 title: null,
                 value: null,
             })
        },
        deleteParameter (parameter) {
             console.log(parameter)
             this.parameters = this.parameters.filter(param => param.id !== parameter.id)
        },
        async onSubmit () {
             try {
                 let data = {
                     weight: this.weight,
                     width: this.width,
                     height: this.height,
                     length: this.length,
                     allow_sales: this.allow_sales,
                     description: this.description,
                     parameters: this.parameters
                 }
                 await axios.patch(`/admin/products/${this.product.id}/update`, data)
                 router.visit(`/admin/products/${this.product.id}`)
             } catch (e) {
                 alert(e.message ?? e)
             }
        },
        async appendSize() {
             try {
                 this.isLoading = true
                 let data = {
                    title: this.newSizeTitle,
                    length: this.newSizeLength,
                    width: this.newSizeWidth,
                    height: this.newSizeHeight,
                 }
                 let response = await axios.post(`/admin/products/${this.product.id}/sizes`, data)
                 let newSize = response.data
                 this.additional_sizes.push(newSize)
                 this.isLoading = false
             } catch (e) {
                 this.isLoading = false
                 if (e?.response?.status === 422) return this.errors = e.response.data.errors
                 if (e?.response?.status === 500) return this.errors = [e.response.message]
                 alert(e?.message ?? e)
             }
        },
        async deleteSize(size) {
           try {
               this.isLoading = true
               await axios.delete(`/admin/products/sizes/${size.id}`)
               let index = this.additional_sizes.indexOf(size)
               this.additional_sizes.splice(index, 1)
               this.isLoading = false
           } catch (e) {
               this.isLoading = false

           }
        }
    }
}
</script>

<style scoped>

</style>
