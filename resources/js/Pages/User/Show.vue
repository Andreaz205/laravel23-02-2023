<template>
    <AuthenticatedLayout>
        <Spinner v-if="isLoading"/>
        <Errors :errors="errors" />

        <div class="row">
            <div class="col-md-12 col-xl-9">
                <div class="card">

                    <div class="card-header p-2 flex items-center">
                        <Link href="/admin/users" class="btn btn-default mr-3">Назад</Link>
                        <ul class="nav nav-pills">

                            <li class="nav-item cursor-pointer"><a class="nav-link active" data-toggle="tab">Карточка
                                клиента</a></li>

                            <Link href="/admin/groups" class="btn btn-default ml-3">Группы</Link>

                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" ref="clientCardRef">
                                <ShowSingleUser v-if="user.kind === 'single'" :user="user" :groups="groups"
                                                :fields="fields"
                                                :change-user-kind="changeUserKind"/>
                                <ShowOrganization v-if="user.kind === 'organization'" :user="user" :groups="groups"
                                                  :change-user-kind="changeUserKind" :fields="fields"/>
                            </div>


                        </div>
                    </div>

                    <div class="card-footer flex justify-end">
                        <button class="btn btn-danger" @click="deleteUser">
                            Удалить пользователя
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xl-3">
                <div class="card">
                    <div class="card-header text-center text-lg">
                        Заказы
                    </div>

                    <ul class="list-group list-group-flush" v-if="orders && orders.length">
                        <li class="list-group-item" v-for="order in orders">
                            <Link :href="`/admin/orders/${order.id}`">
                                Заказ № {{ order.id }}
                            </Link>
                        </li>
                    </ul>
                    <div class="card-body" v-else>
                        Нет заказов!
                    </div>
                </div>

                <div class="card">
                    <div class="card-header text-center text-lg">
                        Транзакции пользователя
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <Link :href="`/admin/users/${user.id}/transactions`">
                                Посмотреть ({{user.transactions_count}})
                            </Link>
                        </li>
                    </ul>
                </div>

                <div class="card">

                    <div class="card-header text-center text-lg">
                        Баллы пользователя
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            {{user.bonuses}}
                        </li>
                    </ul>

                </div>

            </div>
        </div>

    </AuthenticatedLayout>
</template>

<script>
import {Link} from '@inertiajs/vue3'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ShowSingleUser from "@/Pages/User/ShowSingleUser.vue";
import ShowOrganization from "@/Pages/User/ShowOrganization.vue";
import Spinner from "@/Components/Spinner.vue";
import Errors from "@/Components/Errors/Errors.vue";

export default {
    name: "Show",
    components: {Errors, Spinner, ShowOrganization, ShowSingleUser, AuthenticatedLayout, Link},
    props: [
        'fields',
        'userData',
        'groupsData',
        'orders'
    ],
    data() {
        return {
            isLoading: false,
            errors: null,
            user: this.$props.userData,
            groups: this.$props.groupsData,
            newGroup: '',
        }
    },
    methods: {
        async deleteUser() {
            try {
                this.isLoading = true
                await this.$inertia.delete(`/admin/users/${this.user.id}`)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                if (e?.response?.status === 500) return this.errors = [e.response.message]
                alert(e.message ?? e)
            }
        },

        async changeUserKind(kind) {
            try {
                this.isLoading = true
                let response = await axios.patch(`/admin/users/${this.user.id}/kind`, {kind: kind})
                this.user = response.data.data.user
                this.$page.props.fields = response.data.data.fields
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                if (e?.response?.status === 500) return this.errors = [e.response.message]
                alert(e.message ?? e)
            }
        }
    },

}
</script>

<style scoped>

</style>
