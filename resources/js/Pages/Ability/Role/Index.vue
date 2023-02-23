<template>
    <!--    TODO: Модальное окно для создания роли-->
    <div class="modal fade" id="createRoleModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Добваить роль</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label>Введите название</label>
                        <input type="text" v-model="newRole" class="form-control" placeholder="Введите название роли">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть
                    </button>
                    <button type="submit" class="btn btn-primary bg-blue" @click="createRole">Добавить</button>
                </div>
            </div>
        </div>
    </div>
    <AuthenticatedLayout>
        <div class="card card-primary card-outline">
            <div class="card-header flex justify-between items-center">
                <h3 class="card-title">
                    <i class="fas fa-edit"></i>
                    Роли
                </h3>
                <button class="btn btn-primary bg-blue-500"
                        type="button"
                        data-toggle="modal" data-target="#createRoleModal"
                >
                    Добавить
                </button>

            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-5 col-sm-3">
                        <h4>
                            Название
                        </h4>
                    </div>
                    <div class="col-7 col-sm-9">
                       <h4>
                           Доступы
                       </h4>
                    </div>
                </div>
                <template v-if="roles && roles.length">
                    <div class="row">
                        <div class="col-5 col-sm-3">
                            <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
                                <template v-for="role in roles">
                                    <a class="nav-link" id="vert-tabs-home-tab" data-toggle="pill" href="#vert-tabs-home" role="tab" aria-controls="vert-tabs-home" aria-selected="false" @click="setActiveRole(role)">{{role.name}}</a>
                                </template>
<!--                                <a class="nav-link" id="vert-tabs-profile-tab" data-toggle="pill" href="#vert-tabs-profile" role="tab" aria-controls="vert-tabs-profile" aria-selected="false">Profile</a>-->
                                <!--                            <a class="nav-link" id="vert-tabs-messages-tab" data-toggle="pill" href="#vert-tabs-messages" role="tab" aria-controls="vert-tabs-messages" aria-selected="false">Messages</a>-->
                                <!--                            <a class="nav-link active" id="vert-tabs-settings-tab" data-toggle="pill" href="#vert-tabs-settings" role="tab" aria-controls="vert-tabs-settings" aria-selected="true">Settings</a>-->
                            </div>
                        </div>
                        <div class="col-7 col-sm-9">
                            <div v-if="activeRole">
                                {{activeRole.name}}
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <!-- /.card -->
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
export default {
    name: "Index",
    props: ['rolesData'],
    components: {AuthenticatedLayout},
    data () {
        return {
            roles: this.rolesData,
            activeRole: this.rolesData && this.rolesData.length && this.rolesData[0] || null,
            newRole: null,
        }
    },
    methods: {
        setActiveRole(role) {
            this.activeRole = role
        },
        async createRole() {
            let data = {
                role_name: this.newRole
            }
            let response = await axios.post('/admin/roles', data)
        }
    }

}
</script>

<style scoped lang="scss">

</style>
