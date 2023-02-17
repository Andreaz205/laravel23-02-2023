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
    <AuthenticatedLayout>
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
                                        <th>Старая цена</th>
                                        <th>Цена закупки</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="products && products.length">
                                    <tr v-for="product in products">
                                        <td>{{ product.id }}</td>
                                        <td>
                                            <img v-if="product?.images" :src="product?.images[0]?.image_url || '/storage/images/no-image.jpg'" alt="" width="100" height="100">
                                        </td>
                                        <td>
                                            <Link :href="'/admin/products/' + product.id">
                                                {{ product.title }}
                                            </Link>
                                        </td>
                                        <td>{{ product?.quantity }}</td>
                                        <td>{{ product?.variants?.length || 0}} вариантов</td>
                                        <td>{{ product?.price }}</td>
                                        <td>{{ product?.old_price }}</td>
                                        <td>{{ product?.purchase_price }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </AuthenticatedLayout>

</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {router} from '@inertiajs/vue3';
import {Link} from '@inertiajs/vue3';
export default {
    name: "Products",
    components: {AuthenticatedLayout, Link},
    props: [
        'productsData',
        'canProducts'
    ],
    data () {
        return {
            productName: null,
            products: this.$props.productsData
        }
    },
    methods: {
        async storeProduct() {
            let {data: newProduct} = await axios.post('/admin/products', {title: this.productName})
            this.products.push(newProduct)
                // router.visit(
                //     '/admin/product', {
                //     method: 'GET'
                // })
        }
    }
}
</script>

<style scoped>

</style>
