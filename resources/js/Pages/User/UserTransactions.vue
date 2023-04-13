<template>
    <div class="card">
        <div class="card-header text-center text-lg relative">
            Транзакции пользователя {{user.name}}
            <button class="btn btn-default absolute top-1/2 -translate-y-1/2 left-1">
                <Link :href="`/admin/users/${this.user.id}`">
                    К профилю
                </Link>
            </button>
        </div>
    </div>

    <div class="card m-2">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap" v-if="transactions?.length">
                <thead>
                    <tr>
                        <th>
                            ID транзакции
                        </th>
                        <th>
                            Сумма
                        </th>
                        <th>
                            Описание
                        </th>
                        <th>
                            Заказ
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="transaction in transactions">
                        <td>
                            {{transaction}}
                        </td>
                    </tr>
                </tbody>

            </table>
            <div class="text-center p-5">
                У пользователя нет денежный транзакций!
            </div>
        </div>

    </div>
</template>

<script>
import {Link} from '@inertiajs/vue3'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    layout: AuthenticatedLayout,
    name: "UserTransactions",
    components: {Link},
    props: [
        'transactions',
        'user'
    ]
}
</script>

<style scoped>

</style>
