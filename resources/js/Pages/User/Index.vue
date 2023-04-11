<template>
    <AuthenticatedLayout>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="flex justify-between my-3">
                        <div class="flex justify-between items-center gap-x-2">
                            <h3 class="text-lg">Клиенты</h3>

                            <Link href="/admin/groups">
                                <button class="btn btn-default">Группы</button>
                            </Link>

                            <Link href="/admin/user-settings">
                                <button class="btn btn-default">Настройки</button>
                            </Link>

                            <Link href="/admin/users/create" class="btn btn-primary ml-3">
                                Добавить
                            </Link>
                        </div>


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
                                <td>{{user?.group ? user.group.title : 'Вне группы'}}</td>
                                <td>{{user?.group?.discount || 0}}</td>
                                <td>{{user.orders_count}}</td>
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

    }
}
</script>

<style scoped lang="scss">
table {
    min-width: 1100px;
}
</style>
