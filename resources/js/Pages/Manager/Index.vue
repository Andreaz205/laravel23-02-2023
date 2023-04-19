<template>
    <!--    TODO: Модальное окно для редактирования свойства в целом-->
    <div class="modal fade" id="createManagerModal" tabindex="-1" role="dialog" aria-labelledby="optionNameColorModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Редактировать свойство</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Придумайте почту</label>
                        <input type="email" class="form-control" v-model="managerEmailForCreate" placeholder="Введите Email">
                    </div>

                    <div class="form-group">
                        <label>Придумайте имя</label>
                        <input type="text" v-model="managerNameForCreate" class="form-control" placeholder="Введите имя">
                    </div>

                    <div class="form-group">
                        <label>Придумайте пароль</label>
                        <input type="password" class="form-control" v-model="managerPasswordForCreate" placeholder="Введите пароль">

                    </div>

                    <div class="form-group">
                        <label>Подтвердите пароль</label>
                        <input type="password" class="form-control" v-model="managerPasswordConfirmationForCreate" placeholder="Подтвердите пароль">
                    </div>

                    <div class="form-group" v-if="roles && roles.length">
                        <label>Добавить роль</label>
                        <select v-model="role" class="form-control">
                            <option value="destroy">Без роли</option>
                            <option v-for="role in roles" :value="role.id">{{role.name}}</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть</button>
                    <button
                        class="btn btn-primary"
                        @click="createManager"
                    >
                        Добавить
                    </button>
                    <!--                        <button type="button" class="btn btn-primary bg-blue-500" @click="saveOption">Сохранить-->
                    <!--                        </button>-->
                </div>
            </div>
        </div>
    </div>

    <AuthenticatedLayout>

        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-12 text-center text-xl">
                        Менеджеры сайта
                    </div>
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
                            <div class="card-header flex justify-between items-center">
                                <button
                                    v-if="canManagers.create"
                                    type="button" class="btn btn-primary bg-blue"
                                    data-toggle="modal" data-target="#createManagerModal"
                                >
                                    Добавить
                                </button>
<!--                                <Link class="underline font-bold text-blue-400" href="/admin/roles">Роли</Link>-->
<!--                                <Link class="underline font-bold text-blue-400" href="/admin/permissions">Доступы</Link>-->
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                    <tr>
                                        <th>id</th>
                                        <th>Имя</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="managersData && managersData.length">
                                    <tr v-for="manager in managersData">
                                        <td>{{ manager.id }}</td>
                                        <td>
                                            {{ manager.name }}
                                        </td>
                                        <td class="d-flex justify-start">
                                            <Link :href="'/admin/managers/' + manager.id + '/edit'" class="mr-2" v-if="canManagers.edit && user.id !== manager.id">
                                                <i class="fas fa-edit "></i>
                                            </Link>
                                            <a @click="deleteManager(manager)" v-if="canManagers.delete && user.id !== manager.id">
                                                <i class="fas fa-times"></i>
                                            </a>
                                            <div v-if="user.id === manager.id">
                                                Вы
                                            </div>
                                        </td>
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
import {Link} from '@inertiajs/vue3'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Popup from "@/Components/Popup.vue";


export default {
    name: "Index",
    data() {
        return {
            managersData: this.$props.managers,
            managerNameForCreate: null,
            managerEmailForCreate: null,
            managerPasswordForCreate: null,
            managerPasswordConfirmationForCreate: null,
            role: "destroy",
        }
    },
    components: {Popup, AuthenticatedLayout, Link},
    props: ['managers', 'canManagers', 'user', 'roles'],
    methods: {
        async deleteManager(managerForDelete) {
            try {
                let response = await axios.delete(`/admin/managers/${managerForDelete.id}`)
                if (response?.data?.status === 'success') {
                    this.managersData = this.managersData.filter(admin => admin.id !== managerForDelete.id)
                }
            } catch (e) {
                alert(e)
            }
        },
        async createManager() {
            try {

                let data = {
                    name: this.managerNameForCreate,
                    email: this.managerEmailForCreate,
                    password: this.managerPasswordForCreate,
                    password_confirmation: this.managerPasswordConfirmationForCreate,
                    role_id: this.role === 'destroy' ? null : this.role,
                }
                let response = await axios.post('/admin/managers', data)
                let newManager = response.data.data
                this.managersData.push(newManager)

            } catch (e) {
                alert(e)
            }
        }
    }
}
</script>

<style scoped lang="scss">
i {
    &:hover {
        cursor: pointer;
        color: orange;
    }
}

.popup-field {
    input {
        border: 1px solid black;
    }
}
</style>
