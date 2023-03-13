<template>
    <Spinner v-if="isLoading"/>
    <div class="modal fade" id="addSaleModal" tabindex="-1" role="dialog" aria-labelledby="optionNameColorModal"
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
                        <label>Введите название</label>
                        <input type="text" class="form-control" v-model="saleTitle">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть
                    </button>
                    <button type="button" class="btn btn-primary bg-blue-500" @click="addSale">Добавить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <AuthenticatedLayout>
        <template v-if="isFlashOpen">
            <div v-if="$page.props.flash.message" class="border border-red-100 bg-red-50 text-center text-lg related m-4 rounded-xl p-5 relative">
                <span>{{ $page.props.flash.message }}</span>
                <button @click="isFlashOpen = false" class="top-0 right-0 m-4 absolute">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </template>

        <div class="card">
            <div class="card-header text-center text-xl bg-red-400">
                Акции
            </div>

<!--            <div class="card-body">-->
<!--                <template v-if="sales && sales.length">-->
<!--                    <div v-for="sale in sales" class="row border rounded-xl">-->
<!--                        <div class="col-2">-->
<!--                            <Link :href='`/admin/sales/` + sale.id'>{{sale.title}}</Link>-->
<!--                        </div>-->
<!--                        <div class="col-">-->
<!--                            -->
<!--                        </div>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <div v-else class="row">-->
<!--                    <div class="col-12 text-xl text-center">-->
<!--                          У вас нет акций!-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->

            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                    <tr>
                        <th>Название</th>
                        <th>Категории</th>
                        <th>Изображение</th>
                    </tr>
                    </thead>
                    <tbody v-if="sales && sales.length">
                    <tr v-for="sale in sales" @click="visitSale(sale)" :key="sale.id" class="cursor-pointer">
                        <td>
                            <div class="text-lg text-bold">
                                {{ sale.title }}
                            </div>
                        </td>
                        <td>
                            <template v-if="sale.products && sale.products.length">
                                <div>
                                    <template v-for="(product, key) in sale.products" :key="key">

                                            <span>
                                                {{product.title}}
                                            </span>
                                                <span v-if="key < 8">
                                                {{', '}}
                                            </span>

                                    </template>
                                    <Link v-if="sale.products_count > 9" :href="`/admin/sales/${sale.id}`" class="text-bold">
                                        +{{sale.products_count - 9}} товаров
                                    </Link>
                                </div>
                            </template>

                            <div v-else>
                                Нет
                            </div>
                        </td>
                        <td>
                            <div v-if="sale.image_url" class="w-[200px] h-[200px] bg-gray-50 rounded-xl overflow-hidden">
                                <img :src="sale.image_url" alt="" class="object-contain h-full w-full">
                            </div>
                            <div v-else>
                                Нет
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                <button
                    class="btn btn-primary bg-blue"
                    type="button"
                    data-toggle="modal"
                    data-target="#addSaleModal"
                >
                    Добавить
                </button>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<script>
import {router} from '@inertiajs/vue3'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Errors from "@/Components/Errors/Errors.vue";
import Spinner from "@/Components/Spinner.vue";
import {Link} from "@inertiajs/vue3";
export default {
    name: "Index",
    components: {AuthenticatedLayout, Errors, Spinner, Link},
    props: ['sales'],
    data() {
        return {
            isFlashOpen: true,
            isLoading: false,
            errors: null,
            saleTitle: null,
        }
    },
    methods: {
        async addSale() {
            try {
                this.isLoading = true
                let dto = {title: this.saleTitle}
                let {data} = await axios.post('/admin/sales', dto)
                this.saleTitle =  null
                this.sales.unshift(data)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                alert(e?.message ?? e)
            }
        },
        visitSale(sale){
            router.visit(`/admin/sales/${sale.id}`)
        }
    }
}
</script>

<style scoped>

</style>
