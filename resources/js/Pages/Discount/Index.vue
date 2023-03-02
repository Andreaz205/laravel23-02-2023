<template>

    <!--    TODO: Модальное окно для накопительной скидки-->
    <AccumulativeDiscountForm :categories-data="categoriesData?.data" :groups-data="groupsData" @discountCreated="handleDiscountCreated"/>

    <!--    TODO: Модальное окно для скидки по сумме заказа-->
    <OrderDiscountForm :categories-data="categoriesData?.data" :groups-data="groupsData" @discountCreated="handleDiscountCreated"/>

    <!--    TODO: Модальное окно для скидки по купону-->
    <CouponDiscountForm :categories-data="categoriesData?.data" :groups-data="groupsData" @discountCreated="handleDiscountCreated"/>

    <AuthenticatedLayout>
        <div class="card card-primary">
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
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody v-if="discounts?.accumulative_discounts?.discounts && discounts.accumulative_discounts.discounts.length">
                                                    <tr v-for="discount in discounts.accumulative_discounts.discounts">
                                                        <td>{{discount.threshold}} p</td>
                                                        <td>{{discount.allow_discounted ? 'Да' : 'Нет'}}</td>
                                                        <td>{{ discount.allow_kits ? 'Да' : 'Нет'}}</td>
                                                        <td>
                                                            <template v-if="discount.categories && discount.categories.length">
                                                                <span v-for="category in discount.categories">{{category.name}},</span>
                                                            </template>

                                                            <template v-else>
                                                                Все
                                                            </template>
                                                        </td>
                                                        <td>
                                                            <template v-if="discount.available_groups === 'all'">
                                                                Все
                                                            </template>
                                                            <template v-if="discount.available_groups === 'without_groups'">
                                                                Вне группы
                                                            </template>
                                                            <template v-if="
                                                                discount.available_groups === 'selected' &&
                                                                discount.groups &&
                                                                discount.groups.length"
                                                            >
                                                                <span v-for="group in discount.groups">{{group.title}}</span><span> </span>
                                                            </template>
                                                        </td>
                                                        <td>
                                                            {{discount.value}} %
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-danger" @click="deleteDiscount(discount)">
                                                                Удалить
                                                            </button>
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
                                                    <th>Описание скидки</th>
                                                    <th>Минимальная сумма заказа</th>
                                                    <th>Уцененённые товары</th>
                                                    <th>Комплекты товаров</th>
                                                    <th>Категории</th>
                                                    <th>Группы клиентов</th>
                                                    <th>Величина скидки</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody v-if="discounts?.order_discounts?.discounts && discounts.order_discounts.discounts.length">
                                                <tr v-for="discount in discounts.order_discounts.discounts">
                                                    <td>{{discount.description}}</td>
                                                    <td>{{discount.threshold}} p</td>
                                                    <td>{{discount.allow_discounted ? 'Да' : 'Нет'}}</td>
                                                    <td>{{ discount.allow_kits ? 'Да' : 'Нет'}}</td>
                                                    <td>
                                                        <template v-if="discount.categories && discount.categories.length">
                                                            <span v-for="category in discount.categories">{{category.name}},</span>
                                                        </template>

                                                        <template v-else>
                                                            Все
                                                        </template>
                                                    </td>
                                                    <td>
                                                        <template v-if="discount.available_groups === 'all'">
                                                            Все
                                                        </template>
                                                        <template v-else-if="discount.available_groups === 'without_groups'">
                                                            Вне группы
                                                        </template>
                                                        <template v-else-if="
                                                                discount.available_groups === 'selected' &&
                                                                discount.groups &&
                                                                discount.groups.length"
                                                        >
                                                            <span v-for="group in discount.groups">{{group.title}},</span>
                                                        </template>
                                                    </td>
                                                    <td>
                                                        {{discount.value}} %
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-danger" @click="deleteDiscount(discount)">
                                                            Удалить
                                                        </button>
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
                                            <th>Название группы</th>
                                            <th>Величина скидки</th>
                                            <th>Описание скидки</th>
                                            <th>Уценённые товары</th>
                                            <th>Комплекты товаров</th>
                                            <th>Категории</th>
                                        </tr>
                                        </thead>
                                        <tbody v-if="discounts?.group_discounts?.discounts && discounts.group_discounts.discounts.length">
                                        <tr v-for="discount in discounts.group_discounts.discounts">
                                            <td>{{discount.title}}</td>
                                            <td>{{discount.discount}} %</td>
                                            <td>{{discount.discount_description}}</td>
                                            <td>{{discount.allow_discounted ? "Да" : "Нет"}}</td>
                                            <td>{{discount.allow_kits ? "Да" : "Нет"}}</td>
                                            <td>
                                                <template v-if="discount.discounted_categories && discount.discounted_categories.length">
                                                    <span v-for="category in discount.discounted_categories">{{category.name}},</span>
                                                </template>
                                                <template v-else>
                                                    Все
                                                </template>
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
                                            <button
                                                class="btn btn-primary bg-blue-500 mr-2"
                                                type="button"
                                                data-toggle="modal"
                                                data-target="#createCouponDiscountModal"
                                            >
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
                                            <th>Купон</th>
                                            <th>Тип</th>
                                            <th>Минимальная стоимость заказа</th>
                                            <th>Действителен до:</th>
                                            <th>Использован</th>
                                            <th>Описание</th>
                                            <th>Уценённые товары</th>
                                            <th>Комплекты</th>
                                            <th>Категории</th>
                                            <th>Группы</th>
                                            <th>Величина скидки</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="discount in discounts.coupon_discounts.discounts">
                                            <td>{{ discount.coupon_code }}</td>
                                            <td>{{ discount.coupon_type === 'disposable' ? 'Одноразовый' : 'Многоразовый' }}</td>
                                            <td>{{discount.threshold ? discount.threshold + ' р' : ''}}</td>
                                            <td>{{ discount.deadline }}</td>
                                            <td>{{ !discount.used_count ? "Не использовался": `Использован ${discount.used_count} раз(а)` }}</td>
                                            <td>{{discount.description}}</td>
                                            <td>{{discount.allow_discounted ? 'Да' : 'Нет'}}</td>
                                            <td>{{ discount.allow_kits ? 'Да' : 'Нет'}}</td>
                                            <td>
                                                <template v-if="discount.discounted_categories && discount.discounted_categories.length">
                                                    <span v-for="category in discount.discounted_categories">{{category.name}},</span>
                                                </template>
                                                <template v-else>
                                                    Все
                                                </template>
                                            </td>
                                            <td>
                                                <template v-if="discount.available_groups === 'all'">
                                                    Все
                                                </template>
                                                <template v-if="discount.available_groups === 'without_groups'">
                                                    Вне группы
                                                </template>
                                                <template v-if="
                                                                discount.available_groups === 'selected' &&
                                                                discount.groups &&
                                                                discount.groups.length"
                                                >
                                                    <span v-for="group in discount.groups">{{group.title}},</span>
                                                </template>
                                            </td>
                                            <td>{{discount.value}} %</td>
                                            <td>
                                                <button class="btn btn-danger" @click="deleteDiscount(discount)" >
                                                    Удалить
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>

        <BonusesForm :bonus="bonus" :categories="categoriesData.data" :groups="groupsData"/>
    </AuthenticatedLayout>

</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {Link} from '@inertiajs/vue3'
import AccumulativeDiscountForm from "@/Pages/Discount/AccumulativeDiscountForm.vue";
import OrderDiscountForm from "@/Pages/Discount/OrderDiscountForm.vue";
import CouponDiscountForm from "@/Pages/Discount/CouponDiscountForm.vue";
import BonusesForm from "@/Pages/Discount/Bonuses/BonusesForm.vue";
export default {
    name: "Index",
    components: {BonusesForm, OrderDiscountForm,CouponDiscountForm, AccumulativeDiscountForm, AuthenticatedLayout, Link},
    props: ['discountsData', 'categoriesData', 'groupsData', 'bonus'],
    data () {
        return {
            discounts: this.discountsData
        }
    },
    methods: {
        handleDiscountCreated(discount) {
            this.discounts[discount.type + '_discounts'].discounts.push(discount)
        },
        async toggleAvailabilityDiscount(discountType) {
            try {
                let data = {type: discountType}
                let response = await axios.patch('/admin/discounts/toggle-availability', data)
                let isAvailable = response.data?.is_available
                this.discounts[discountType + '_discounts'].is_available = isAvailable
            } catch (e) {
                alert(e?.response?.data?.errors ?? e?.message ?? e)
            }
        },
        async deleteDiscount(discount) {
            try {
                await axios.delete(`/admin/discounts/${discount.id}`)
                this.discounts[discount.type + '_discounts'].discounts = this.discounts[discount.type + '_discounts'].discounts.filter(d => d.id !== discount.id)
            } catch (e) {
                alert(e?.response?.data?.message ?? e?.message ?? e)
            }
        }
    }

}
</script>

<style scoped>

</style>
