<template>
    <Popup
        :is-open-prop="isCreateManagerPopupOpened"
        @close="toggleManagerPopup(false)"
        :create-manager="createManager"
    />
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
                                <a
                                    v-if="canManagers.create"
                                    @click="toggleManagerPopup(true)"
                                    class="btn btn-primary"
                                >
                                    Добавить
                                </a>
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
                                            <Link :href="'/admin/managers/' + manager.id + '/edit'" class="mr-2" v-if="canManagers.edit">
                                                <i class="fas fa-edit "></i>
                                            </Link>
                                            <a @click="deleteManager(manager)" v-if="canManagers.delete">
                                                <i class="fas fa-times"></i>
                                            </a>
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
            isCreateManagerPopupOpened: false,
            managerNameForCreate: null,
            managerEmailForCreate: null,
            managerPasswordForCreate: null,
            managerPasswordConfirmationForCreate: null,
        }
    },
    components: {Popup, AuthenticatedLayout, Link},
    props: ['managers', 'canManagers'],
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
        toggleManagerPopup(flag) {
            this.isCreateManagerPopupOpened = flag
        },
        async createManager(name, email, password, confirmedPassword) {
            try {

                let data = {
                    name: name,
                    email: email,
                    password: password,
                    password_confirmation: confirmedPassword,
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
