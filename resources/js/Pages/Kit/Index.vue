<template>
    <!--TODO: Модальное окно для создания комплекта-->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить комплект</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form>
                        <div class="form-group">
                            <label for="inputAddress">Название</label>
                            <input type="text" v-model="kitName" class="form-control" id="inputAddress" placeholder="Введите название">
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary bg-blue-500" @click="storeKit">Добавить</button>
                </div>
            </div>
        </div>
    </div>

    <!--TODO: Модальное окно привязки товаров к комплекту-->
    <div class="modal fade" id="bindProducts" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить товары</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <template v-if="products && products.length">
                        <div v-for="product in products" :key="product.id + '-' + selectedKit?.id" class="container-fluid">
                            <div class="row">
                                <div class="col-8">
                                    <span>{{product.title}}</span>
                                </div>
                                <div class="col-4">
                                    <CustomSwitch
                                        :is-checked="product.is_exists"
                                        :switch-id="`bind-kit-${product.id}`"
                                        @changeSwitch="toggle(product)"
                                    />
                                </div>
                            </div>

                        </div>
                    </template>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <AuthenticatedLayout>
        <div class="card">
            <div class="card-header">
                <button class="btn btn-default mr-2">
                    <Link href="/admin/products">
                        Назад
                    </Link>
                </button>
                <button
                    v-if="canProducts.create"
                    type="button" class="btn btn-primary bg-blue-500" data-toggle="modal" data-target="#exampleModal"
                >
                    Добавить
                </button>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap" v-if="kits && kits.length">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <!--                                        <Link :href="'/admin/kits/' + kit.id" v-for="kit in kits">-->
                    <tr v-for="kit in kits" class="cursor-pointer" :title="kit.title">
                        <td>{{ kit.id }}</td>
                        <td>
                            {{ kit.title }}
                        </td>
                        <td>
                            <button class="btn btn-default"
                                    @click="fetchProductsForKit(kit)"
                                    type="button"
                                    data-toggle="modal"
                                    data-target="#bindProducts"
                            >
                                Добавить товары
                            </button>
                        </td>
                    </tr>
                    <!--                                        </Link>-->
                    </tbody>

                </table>
                <div v-else class="h-96 flex items-center justify-center text-xl">
                        У вас пока нет комплектов!
                </div>
            </div>
            <div class="card-footer clearfix" v-if="pagination && kits && kits.length">
                <ul class="flex justify-start items-center">
                    <li  v-if="kitsData.current_page !== 1" class="h-[32px] cursor-pointer border-[1px] p-1 rounded-sm border-blue-500 bg-white mx-[2px]">
                        <Link :href="kitsData.prev_page_url || ''" >«</Link>
                    </li>

                    <li v-for="link in calcPagination">
                        <!--                                        <span :class="{active : link.active}" class="page-link" @click="fetchOrdersPage(link.url)">{{link.label}}</span>-->
                        <span
                            :class="['cursor-pointer border-[1px] rounded-sm border-blue-500 bg-white p-1 mx-[2px]', {'bg-gray': link.active}]"
                            @click="fetchKitsPage(link.url)"
                        >{{link.label}}</span>
                    </li>

                    <li v-if=" kitsData.current_page !== kitsData.last_page" class="h-[32px] cursor-pointer border-[1px] p-1 rounded-sm border-blue-500 bg-white mx-[2px]">
                        <Link :href="kitsData.next_page_url || ''">»</Link>
                    </li>
                </ul>
            </div>
        </div>
    </AuthenticatedLayout>

</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {Link, router} from '@inertiajs/vue3'
import CustomSwitch from "@/Components/CustomSwitch.vue";
export default {
    name: "Index",
    components: {CustomSwitch, AuthenticatedLayout, Link},
    props: ['kitsData', 'canProducts'],
    data () {
        return {
            products: [],
            selectedKit: null,
            kits: this.kitsData.data,
            pagination: this.kitsData.links,
            kitName: null,
        }
    },
    methods: {
        async fetchKitsPage(url) {
            router.visit(url)
        },
        async storeKit() {
            try {
                let {data: newKit} = await axios.post('/admin/kits', {title: this.kitName})
                this.kits.push(newKit.data)
                this.kitName = null
            } catch (e) {
                alert(e.message ?? e)
            }
        },
        async fetchProductsForKit(kit) {
            try {
                this.changeSelectedKit(kit)
                let response = await axios.get(`/admin/kits/${kit.id}/products`)
                this.products = response.data
            } catch (e) {
                alert(e.message ?? e)
            }
        },
        async toggle(product) {
            try {
                let selectedKit = this.selectedKit
                let response = await axios.get(`/admin/kits/${this.selectedKit.id}/products/${product.id}/toggle`)
                console.log(response)
            } catch (e) {
                alert(e.message ?? e)
            }
        },
        changeSelectedKit(kit) {
            this.selectedKit = kit
        }
    },
    computed: {
        calcPagination () {
            return this.pagination?.slice(1, -1)
        }
    }

}
</script>

<style scoped>

</style>
