<template>
    <AuthenticatedLayout>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Клиенты</h3>

                        <Link href="/admin/users/create" class="btn btn-primary">
                            Добавить
                        </Link>

                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="searchTerm">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default" @click="search(searchTerm)">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                            <tr>
                                <th>Название</th>
                                <th>Тип</th>
                                <th>Дата создания</th>
                                <th>Телефон</th>
                                <th>Почта</th>
                                <th>Группа</th>
                                <th>Скидка</th>
                                <th>Заказов</th>
                            </tr>
                            </thead>
                            <tbody v-if="users && users.length">
                            <tr v-for="user in users">
                                <td><Link :href="'/admin/users/' + user.id">{{user.name}}</Link></td>
                                <td>{{user.kind === 'single' ? 'Физическое лицо' : 'Организация'}}</td>
                                <td>{{calculateDate(user.created_at)}}</td>
                                <td><span class="tag tag-success">{{user.phone}}</span></td>
                                <td>{{user.email}}</td>
                                <td>{{user?.group}}</td>
                                <td>{{user?.sale}}</td>
                                <td>{{user.ordersQuantity}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Link} from "@inertiajs/vue3";
export default {
    name: "Index",
    components: {AuthenticatedLayout, Link},
    props: [
        'usersData'
    ],
    data() {
        return {
            users: this.$props.usersData?.data,
            pagination: this.$props.usersData,
            searchTerm: null
        }
    },
    methods: {
        calculateDate(date) {
            let value = date.split('T')[0]
            let time = date.split('T')[1].split('.')[0]
            return time + ' ' + value
        },
        async search(term) {
            try {
                let response = await axios.get(`/admin/users/by-term?term=${term}`)
                this.users = response?.data?.data
                this.pagination = response?.data
            } catch (e) {
                alert(e)
            }
        }
    },
    computed: {

    },
    mounted() {
        console.log(this.pagination)
        console.log(this.usersData)
    }
}
</script>

<style scoped lang="scss">
table {
    min-width: 1100px;
}
</style>
