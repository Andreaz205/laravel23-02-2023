<template>

    <!--    TODO: Модальное окно для накопительной скидки-->
    <AccumulativeDiscountForm :categories-data="categoriesData?.data" :groups-data="groupsData"/>

    <!--    TODO: Модальное окно для скидки по сумме заказа-->
    <div class="modal fade" id="createOrderDiscountModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить свойства</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    Hello world!!!

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть
                    </button>
                    <button type="button" class="btn btn-primary bg-blue-500" @click="createDiscount">
                        Создать скидку
                    </button>
                </div>
            </div>
        </div>
    </div>

    <AuthenticatedLayout>
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Скидки и бонусы</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 text-center mt-2 mb-5">
                        <span class="text-xl">Скидки</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-2 col-md-4">
                        <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist"
                             aria-orientation="vertical">
                            <a class="nav-link  active" id="accumulative-discounts-tab" data-toggle="pill"
                               href="#vert-tabs-home" role="tab" aria-controls="accumulative-discounts"
                               aria-selected="true">
                                Накопительные скидки
                                ({{ discounts?.accumulative_discounts?.is_available ? 'Включены' : 'Выключены' }})
                            </a>
                            <a class="nav-link" id="order-sum-discounts-tab" data-toggle="pill"
                               href="#order-sum-discounts" role="tab" aria-controls="order-sum-discounts"
                               aria-selected="false">
                                По сумме заказа
                                ({{ discounts?.order_discounts?.is_available ? 'Включены' : 'Выключены' }})
                            </a>
                            <a class="nav-link" id="user-group-discounts-tab" data-toggle="pill"
                               href="#user-group-discounts" role="tab" aria-controls="user-group-discounts"
                               aria-selected="false">
                                По группе клиента
                                ({{ discounts?.group_discounts?.is_available ? 'Включены' : 'Выключены' }})
                            </a>
                            <a class="nav-link" id="coupon-discounts-tab" data-toggle="pill" href="#coupon-discounts"
                               role="tab" aria-controls="coupon-discounts" aria-selected="false">
                                По купонам
                                ({{ discounts?.coupon_discounts?.is_available ? 'Включены' : 'Выключены' }})
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-10 col-md-8">
                        <div class="tab-content" id="accumulative-discounts">
                            <div class="tab-pane fade active show" id="vert-tabs-home" role="tabpanel"
                                 aria-labelledby="vert-tabs-home-tab">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="my-2">
<!--                                                v-if="canDiscounts.edit"-->
                                                <button
                                                    class="btn btn-primary bg-blue-500 mr-2"
                                                    type="button"
                                                    data-toggle="modal"
                                                    data-target="#createAccumulativeDiscountModal"
                                                >
                                                    Добавить
                                                </button>
                                                <button class="btn btn-secondary mr-2"
                                                        v-if="discounts?.accumulative_discounts?.is_available"
                                                        @click="toggleAvailabilityDiscount('accumulative')">
                                                    Выключить
                                                </button>
                                                <button class="btn btn-primary" v-else
                                                        @click="toggleAvailabilityDiscount('accumulative')">
                                                    Включить
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="container-fluid table-responsive">
                                                <table class="table table-hover text-nowrap border-x-2 border-b-2">
                                                    <thead>
                                                    <tr>
                                                        <th>Стоимость заказов</th>
                                                        <th>Уцененённые товары</th>
                                                        <th>Комплекты товаров</th>
                                                        <th>Категории</th>
                                                        <th>Группы клиентов</th>
                                                        <th>Величина скидки</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody v-if="discounts?.accumulative_discounts?.discounts && discounts.accumulative_discounts.discounts.length">
                                                    <tr v-for="discount in discounts.accumulative_discounts.discounts">
                                                        <td>{{discount.threshold}}</td>
                                                        <td>John Doe</td>
                                                        <td>11-7-2014</td>
                                                        <td>11-7-2014</td>
                                                        <td><span class="tag tag-success">Approved</span></td>
                                                        <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback
                                                            doner.
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="order-sum-discounts" role="tabpanel"
                                 aria-labelledby="order-sum-discounts-tab">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="my-2">
                                            <button
                                                    class="btn btn-primary bg-blue-500 mr-2"
                                                    type="button"
                                                    data-toggle="modal"
                                                    data-target="#createOrderDiscountModal"
                                            >
                                                Добавить
                                            </button>
                                            <button class="btn btn-secondary mr-2"
                                                    v-if="discounts?.order_discounts?.is_available"
                                                    @click="toggleAvailabilityDiscount('order')"
                                            >
                                                Выключить
                                            </button>
                                            <button class="btn btn-primary" v-else
                                                    @click="toggleAvailabilityDiscount('order')"
                                            >
                                                Включить
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="container-fluid table-responsive">
                                            <table class="table table-hover text-nowrap border-x-2 border-b-2">
                                                <thead>
                                                <tr>
                                                    <th>Стоимость заказов</th>
                                                    <th>Уцененённые товары</th>
                                                    <th>Комплекты товаров</th>
                                                    <th>Категории</th>
                                                    <th>Группы клиентов</th>
                                                    <th>Величина скидки</th>
                                                </tr>
                                                </thead>
                                                <tbody v-if="discounts?.order_discounts?.discounts && discounts.order_discounts.discounts.length">
                                                <tr v-for="discount in discounts.order_discounts.discounts">
                                                    <td>{{discount.threshold}}</td>
                                                    <td>John Doe</td>
                                                    <td>11-7-2014</td>
                                                    <td>11-7-2014</td>
                                                    <td><span class="tag tag-success">Approved</span></td>
                                                    <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback
                                                        doner.
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="user-group-discounts" role="tabpanel"
                                 aria-labelledby="user-group-discounts-tab">
                                <div class="row">
                                    <div class="col-12 text-blue-300">
                                        <Link href="/admin/groups">Перейти к редактированию групп клиентов</Link>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="my-2">
                                            <button class="btn btn-primary mr-2"

                                            >
                                                Добавить
                                            </button>
                                            <button class="btn btn-secondary mr-2"
                                                    v-if="discounts?.group_discounts?.is_available"
                                                    @click="toggleAvailabilityDiscount('group')"
                                            >
                                                Выключить
                                            </button>
                                            <button class="btn btn-primary" v-else
                                                    @click="toggleAvailabilityDiscount('group')"
                                            >
                                                Включить
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="container-fluid table-responsive">
                                    <table class="table table-hover text-nowrap border-x-2 border-b-2">
                                        <thead>
                                        <tr>
                                            <th>Стоимость заказов</th>
                                            <th>Уцененённые товары</th>
                                            <th>Комплекты товаров</th>
                                            <th>Категории</th>
                                            <th>Группы клиентов</th>
                                            <th>Величина скидки</th>
                                        </tr>
                                        </thead>
                                        <tbody v-if="discounts?.group_discounts?.discounts && discounts.group_discounts.discounts.length">
                                        <tr v-for="discount in discounts.group_discounts.discounts">
                                            <td>{{discount.threshold}}</td>
                                            <td>John Doe</td>
                                            <td>11-7-2014</td>
                                            <td>11-7-2014</td>
                                            <td><span class="tag tag-success">Approved</span></td>
                                            <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback
                                                doner.
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

<!--                                <table class="table table-hover text-nowrap border-x-2 border-b-2">-->
<!--                                    <thead>-->
<!--                                    <tr>-->
<!--                                        <th>ID</th>-->
<!--                                        <th>User</th>-->
<!--                                        <th>Date</th>-->
<!--                                        <th>Status</th>-->
<!--                                        <th>Reason</th>-->
<!--                                    </tr>-->
<!--                                    </thead>-->
<!--                                    <tbody>-->
<!--                                    <tr>-->
<!--                                        <td>183</td>-->
<!--                                        <td>John Doe</td>-->
<!--                                        <td>11-7-2014</td>-->
<!--                                        <td><span class="tag tag-success">Approved</span></td>-->
<!--                                        <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>-->
<!--                                    </tr>-->
<!--                                    <tr>-->
<!--                                        <td>219</td>-->
<!--                                        <td>Alexander Pierce</td>-->
<!--                                        <td>11-7-2014</td>-->
<!--                                        <td><span class="tag tag-warning">Pending</span></td>-->
<!--                                        <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>-->
<!--                                    </tr>-->
<!--                                    <tr>-->
<!--                                        <td>657</td>-->
<!--                                        <td>Bob Doe</td>-->
<!--                                        <td>11-7-2014</td>-->
<!--                                        <td><span class="tag tag-primary">Approved</span></td>-->
<!--                                        <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>-->
<!--                                    </tr>-->
<!--                                    <tr>-->
<!--                                        <td>175</td>-->
<!--                                        <td>Mike Doe</td>-->
<!--                                        <td>11-7-2014</td>-->
<!--                                        <td><span class="tag tag-danger">Denied</span></td>-->
<!--                                        <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>-->
<!--                                    </tr>-->
<!--                                    </tbody>-->
<!--                                </table>-->
                            </div>
                            <div class="tab-pane fade" id="coupon-discounts" role="tabpanel"
                                 aria-labelledby="coupon-discounts-tab">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="my-2">
                                            <button class="btn btn-primary mr-2">
                                                Добавить
                                            </button>
                                            <button class="btn btn-secondary mr-2"
                                                    v-if="discounts?.coupon_discounts?.is_available"
                                                    @click="toggleAvailabilityDiscount('coupon')"
                                            >
                                                Выключить
                                            </button>
                                            <button
                                                class="btn btn-primary" v-else
                                                @click="toggleAvailabilityDiscount('coupon')"
                                            >
                                                Включить
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-hover text-nowrap border-x-2 border-b-2">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>User</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="discount in discounts.coupon_discounts.discounts">
                                            <td>segh</td>
                                            <td>John Doe</td>
                                            <td>11-7-2014</td>
                                            <td>11-7-2014</td>
                                            <td>
                                                <span class="tag tag-success">Approved</span>
                                            </td>
                                            <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback
                                                doner.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 text-center mt-2 mb-5">
                        <span class="text-xl">Бонусы</span>
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </AuthenticatedLayout>

</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {Link} from '@inertiajs/vue3'
import AccumulativeDiscountForm from "@/Pages/Discount/AccumulativeDiscountForm.vue";
export default {
    name: "Index",
    components: {AccumulativeDiscountForm, AuthenticatedLayout, Link},
    props: ['discountsData', 'categoriesData', 'groupsData'],
    data () {
        return {
            discounts: this.discountsData
        }
    },
    methods: {
        async toggleAvailabilityDiscount(discountType) {
            try {
                let data = {type: discountType}
                let response = await axios.patch('/admin/discounts/toggle-availability', data)
                let isAvailable = response.data?.is_available
                this.discounts[discountType + '_discounts'].is_available = isAvailable
            } catch (e) {
                alert(e?.response?.data?.errors ?? e?.message ?? e)
            }
        }
    }

}
</script>

<style scoped>

</style>
