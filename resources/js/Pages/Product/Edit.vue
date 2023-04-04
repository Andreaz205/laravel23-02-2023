<template>
    <AuthenticatedLayout>
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Редактировать информацию о товаре</h3>
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

export default {
    name: "Edit",
    components: {CustomSwitch, AuthenticatedLayout},
    props: ['productData'],
    data () {
        return {
            weight: this.productData.weight,
            product: this.productData,
            parameters: this.productData.parameters,
            width: this.productData.width,
            height: this.productData.height,
            length: this.productData.length,
            allow_sales: this.productData.allow_sales,
            description: this.productData.description,

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
                 let response = await axios.patch(`/admin/products/${this.product.id}/update`, data)
                 router.visit(`/admin/products/${this.product.id}`)
             } catch (e) {
                 alert(e.message ?? e)
             }

        }
    }
}
</script>

<style scoped>

</style>
