<template>
    <Spinner v-if="isLoading"/>
    <div class="card">
        <div class="card-header text-center text-xl">
            Транзакции клиентов
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-head-fixed text-nowrap">
                <thead>
                <tr>
                    <th>ID транзакции</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                    <th>Дата</th>
                    <th>Описание</th>
                    <th>Пользователь</th>
                    <th>Заказ</th>
                </tr>
                </thead>
                <tbody v-if="transactions.data && transactions.data.length">
                <tr v-for="transaction in transactions.data">
                    <td>{{transaction.id}}</td>
                    <td>{{transaction.amount}}</td>
                    <td>{{transaction.status}}</td>
                    <td>{{transaction.created_at}}</td>
                    <td>{{transaction.description}}</td>
                    <td>
                        <span v-if="transaction.user">
                            <Link :href="`/admin/users/${transaction.user.id}`">
                                {{transaction.user.name}}
                            </Link>
                        </span>
                        <span v-else>
                            Пользователь не зарегестрирован
                        </span>

                    </td>
                    <td>
                      <span>
                            <Link :href="`/admin/orders/${transaction.order?.id}`">
                                {{transaction.order?.id}}
                            </Link>
                        </span>
                    </td>
<!--                    <td><span class="tag tag-success">{{user.phone}}</span></td>-->
<!--                    <td>{{user.email}}</td>-->
<!--                    <td>{{user?.group ? user.group.title : 'Вне группы'}}</td>-->
<!--                    <td>{{user?.group?.discount || 0}}</td>-->
<!--                    <td>{{user.orders_count}}</td>-->
                </tr>
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            <Pagination :items="transactions" :fetch-page="fetchPage"/>
        </div>
    </div>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import {router} from "@inertiajs/vue3";
import {Link} from '@inertiajs/vue3'
import Spinner from "@/Components/Spinner.vue";

export default {
    name: "Index",
    components: {Spinner, Pagination, Link},
    layout: AuthenticatedLayout,
    props: ['transactions'],
    data () {
        return {
            isLoading: false
        }
    },
    methods: {
        async fetchPage(url) {
            router.visit(url)
        }
    },
}
</script>

<style scoped>
    td {
        text-align: center;
    }
</style>
