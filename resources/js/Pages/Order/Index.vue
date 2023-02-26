<template>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Заказы</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Номер</th>
                        <th>Дата доставки</th>
                        <th>Сумма</th>
                        <th>Цена доставки</th>
                        <th>Оплачен</th>
                        <th>Статус</th>
                        <th>Имя</th>
                        <th>Телефон</th>
                        <th>Почта</th>
                        <th>Вариант оплаты</th>
                        <th>Доставка</th>
                        <th>Комментарий клиента</th>
                        <th>Комментарий менеджера</th>
                        <th>Адрес</th>
                        <th>Ответственный</th>
                        <th>Создан</th>
                    </tr>
                    </thead>
                    <tbody v-if='orders?.data'>
                    <OrderRow v-for="order in orders.data" :order="order" :format-date="this.formatDate" />
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix" v-if="orders">
                <ul class="pagination pagination-sm m-0 float-right">
                    <li class="page-item" v-if="orders.current_page !== 1">
                        <a class="page-link" href="#" >«</a>
                    </li>

                    <li class="page-item" v-for="link in calcPagination">
                        <span :class="{active : link.active}" class="page-link" @click="fetchOrdersPage(link.url)">{{link.label}}</span>
                    </li>

                    <li class="page-item" v-if="orders.current_page !== orders.last_page">
                        <a class="page-link" href="#">»</a>
                    </li>
                </ul>
            </div>
        </div>


</template>

<script>
import axios from "axios";
import OrderRow from "@/Pages/Order/OrderRow.vue";
import {formatDate} from "/resources/js/utils/formatDate";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
export default {
    name: "Orders",
    components: {AuthenticatedLayout, OrderRow},
    layout: AuthenticatedLayout,
    data () {
        return {
            orders: null,
            pagination: null
        }
    },
    computed: {
        calcPagination () {
            return this.pagination?.slice(1, -1)
        }
    },
    methods: {
        formatDate(date) {
            return  formatDate(date)
        },
        async initData() {
            let {data} = await axios.get('/admin/orders/data')
            this.orders = data
            this.pagination = data.links
            console.log(this.orders)
        },
        async fetchOrdersPage(url) {
            let {data} = await axios.get(url)
            this.orders = data
            this.pagination = data.links
            console.log(data)
        }
    },
    mounted() {
        this.initData()
    }
}
</script>

<style scoped lang="scss">
.active{
    background: grey;
}

tr{
    &:hover {
        cursor: pointer;
        background: orange;
    }
}
</style>
