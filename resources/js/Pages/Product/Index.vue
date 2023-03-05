<template>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить товар</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form>
                        <div class="form-group">
                            <label for="inputAddress">Название</label>
                            <input type="text" v-model="productName" class="form-control" id="inputAddress" placeholder="Введите название">
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary bg-blue-500" @click="storeProduct">Добавить</button>
                </div>
            </div>
        </div>
    </div>
<!--    <AuthenticatedLayout>-->
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Товары</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Главная</a></li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <button
                                    v-if="canProducts.create"
                                    type="button" class="btn btn-primary bg-blue-500" data-toggle="modal" data-target="#exampleModal"
                                >
                                    Добавить
                                </button>
                                <button class="btn btn-default ml-4">
                                    <Link href="/admin/kits">
                                        Комплекты
                                    </Link>
                                </button>
                                <button class="btn btn-default ml-4">
                                    <Link href="/admin/prices">
                                        Цены
                                    </Link>
                                </button>
                                <button class="btn btn-default ml-4">
                                    <Link href="/admin/options">
                                        Свойства
                                    </Link>
                                </button>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Фото</th>
                                        <th>Название</th>
                                        <th>Остаток</th>
                                        <th>Артикул</th>
                                        <th>Цена</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="products && products.length">
<!--                                        <Link :href="'/admin/products/' + product.id" v-for="product in products">-->
                                            <tr v-for="product in products" @click="visitProduct(product)" class="cursor-pointer h-[100px]" :title="product.title">
                                                <td>{{ product.id }}</td>
                                                <td>
                                                    <img v-if="product?.images" :src="product?.images[0]?.image_url || '/storage/images/no-image.jpg'" alt="" width="100" height="100">
                                                    <img v-else src="/storage/images/no-image.jpg" alt="" width="100" height="100">
                                                </td>
                                                <td>
                                                    {{ product.title }}
                                                </td>
                                                <td>{{ product?.quantity }}</td>
                                                <td>{{ product?.variants_count}} вариантов</td>
                                                <td>{{ product?.min_max_price['min_price']}} P  -
                                                    {{ product?.min_max_price['max_price'] }} P</td>
                                            </tr>
<!--                                        </Link>-->
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer clearfix" v-if="pagination">
                                <ul class="flex justify-start items-center">
                                    <li  v-if="productsData.current_page !== 1" class="h-[32px] cursor-pointer border-[1px] p-1 rounded-sm border-blue-500 bg-white mx-[2px]">
                                        <Link :href="productsData.prev_page_url" >«</Link>
                                    </li>

                                    <li v-for="link in calcPagination">
<!--                                        <span :class="{active : link.active}" class="page-link" @click="fetchOrdersPage(link.url)">{{link.label}}</span>-->
                                        <span
                                            :class="['cursor-pointer border-[1px] rounded-sm border-blue-500 bg-white p-1 mx-[2px]', {'bg-gray': link.active}]"
                                            @click="fetchProductsPage(link.url)"
                                        >{{link.label}}</span>
                                    </li>

                                    <li v-if="productsData.current_page !== productsData.last_page" class="h-[32px] cursor-pointer border-[1px] p-1 rounded-sm border-blue-500 bg-white mx-[2px]">
                                        <Link :href="productsData.next_page_url">»</Link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
<!--    </AuthenticatedLayout>-->

</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {router, Link} from '@inertiajs/vue3';
export default {
    name: "Products",
    components: {AuthenticatedLayout, Link},
    layout: AuthenticatedLayout,
    props: [
        'productsData',
        'canProducts'
    ],
    data () {
        return {
            productName: null,
            products: this.$props.productsData?.data,
            pagination: this.productsData?.links
        }
    },
    methods: {
        fetchProductsPage(url) {
            router.visit(url)
        },
        async storeProduct() {
            let {data: newProduct} = await axios.post('/admin/products', {title: this.productName})
            this.products.push(newProduct)
                // router.visit(
                //     '/admin/product', {
                //     method: 'GET'
                // })
        },
        visitProduct(product) {
            router.visit(`/admin/products/${product.id}`)
        },
    },
    computed: {
        calcPagination () {
            return this.pagination?.slice(1, -1)
        }
    },
    mounted() {
        console.log()
    }
}
</script>

<style scoped>
    td{
        vertical-align: middle
    }
</style>
