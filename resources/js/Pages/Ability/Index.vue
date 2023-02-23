<template>

    <!--    TODO: Модальное окно для редактирования свойств-->
    <div class="modal fade" id="createOptionsModal" tabindex="-1" role="dialog" aria-labelledby="createRoleModel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить роль</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Введите название</label>
                        <input type="text" v-model="newRoleName" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть
                    </button>
                    <button type="button" class="btn btn-primary bg-blue-500" @click="addRole">
                        Добавить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <AuthenticatedLayout>
        <div class="card">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">
                        <div>Роли</div>
                        <button
                            class="text-blue-300"
                            type="button"
                            data-toggle="modal"
                            data-target="#createOptionsModal"
                        >
                            Добавить роль
                        </button>
                    </th>
                    <template v-if="roles && roles.length">
                        <th scope="col" v-for="role in roles" class="text-center">
                            <div>
                                {{role.name}}
                            </div>
                            <button class="btn btn-danger" @click="deleteRole(role)">Удалить</button>
                        </th>
                    </template>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="section in sections">
                        <table class="table table-bordered nested-section" >
                            <tbody >
                            <tr>
                                <td style="width: 200px;">
                                    {{section.name}}
                                </td>
                                <td>
                                    <table class="table table-bordered nested">
                                        <tr>
                                            <td>Чтение</td>
                                        </tr>
                                        <tr>
                                            <td>Добавление</td>
                                        </tr>
                                        <tr>
                                            <td>Изменение</td>
                                        </tr>
                                        <tr>
                                            <td>Удаление</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <template v-if="roles && roles.length">
                            <td v-for="role in roles" style="width: 200px;">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <div class="flex justify-center items-center">
                                                <CustomSwitch :is-checked="isChecked(role, section, 'list')" @changeSwitch="onSwitchChange('list', section, role)" :switch-id="`custom-switch-row-${section.id}-col-${role.id}-read`" />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="flex justify-center items-center">
                                                <CustomSwitch :is-checked="isChecked(role, section, 'create')" @changeSwitch="onSwitchChange('create', section, role)" :switch-id="`custom-switch-row-${section.id}-col-${role.id}-add`" />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="flex justify-center items-center">
                                                <CustomSwitch :is-checked="isChecked(role, section, 'edit')" @changeSwitch="onSwitchChange('edit', section, role)" :switch-id="`custom-switch-row-${section.id}-col-${role.id}-edit`" />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="flex justify-center items-center">
                                                <CustomSwitch :is-checked="isChecked(role, section, 'delete')" @changeSwitch="onSwitchChange('delete', section, role)" :switch-id="`custom-switch-row-${section.id}-col-${role.id}-delete`" />
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </template>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CustomSwitch from '@/Components/CustomSwitch.vue'
import siteSectionsData from '/resources/js/utils/site-sections.json'
export default {
    name: "PermissionsRoles",
    components: {AuthenticatedLayout, CustomSwitch},
    props: ['rolesData', 'sectionsData'],
    data () {
        return {
            newRoleName: null,
            sections: this.sectionsData,
            roles: this.rolesData.filter(role => role.name !== 'super-admin')
        }
    },
    methods: {
        async onSwitchChange (type, section, role) {
            try {
                await axios.patch(`/admin/abilities/roles/${role.id}/permissions/${section.id}?type=${type}`)
            } catch (e) {
                alert(e)
            }
        },
        async addRole() {
            try {
                let data = {
                    role_name: this.newRoleName
                }
                let response = await axios.post('/admin/roles', data)
                console.log(response)
                this.roles.push(response.data)
                this.newRoleName = null
            } catch (e) {
                alert(e)
            }
        },
        async deleteRole(role) {
            try {
                let isConfirmed = confirm('Вы действительно хотите удалить роль ' + role.name)
                if (!isConfirmed) return
                await axios.delete(`/admin/roles/${role.id}`)
                this.roles = this.roles.filter(r => r.id !== role.id)
            } catch (e) {
                alert(e)
            }
        },
        isChecked(role, section, type) {
            return role.permissions?.find(permission => permission?.name === (section.name + ' ' + type))
        }
    }
}
</script>

<style lang="scss" scoped>
.nested{
    tr{
        td{
            height: 64px;
        }
    }
}
.nested-section{
    margin-bottom: 0;
}
</style>
