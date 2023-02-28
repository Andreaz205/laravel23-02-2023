<template>
    <AuthenticatedLayout>
        <div class="card">
            <div class="card-header">
                <div class="flex items-center gap-x-2">
                    <div class=" ml-2 btn btn-default">
                        <Link href="/admin/managers">Назад</Link>
                    </div>
                    <div>
                        Редактировать мнеджера {{ manager.name }}
                    </div>
                </div>

            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Имя</label>
                                <input type="text" v-model="name" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="roles && roles.length">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Роль</label>
                                <select class="form-control" v-model="selectedRoleId">
                                    <option value="destroy">Без роли</option>
                                    <option v-for="role in roles" :value="role.id">{{role.name}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-center items-center text-3xl">Права пользователя</div>
                <template v-if="sections && sections.length">
                    <div class="row" v-for="section in sections">
                        <div class="col-12">
                            <div class="text-lg">{{ translateSection(section.name) }}</div>
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label class="mr-2">Чтение</label>
                                        <input type="checkbox" v-model="section.list_input" class="form-control" :checked="manager.permissions?.find(perm => perm.name.includes(section.name + ' list'))">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label class="mr-2">Создание</label>
                                        <input type="checkbox" v-model="section.create_input" class="form-control" :checked="manager.permissions?.find(perm => perm.name.includes(section.name + ' create'))">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label class="mr-2">Редактирование</label>
                                        <input type="checkbox" v-model="section.edit_input" class="form-control" :checked="manager.permissions?.find(perm => perm.name.includes(section.name + ' edit'))">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label class="mr-2">Удаление</label>
                                        <input type="checkbox" v-model="section.delete_input" class="form-control" :checked="manager.permissions?.find(perm => perm.name.includes(section.name + ' delete'))">
                                    </div>
                                </div>
                            </div>
                            <div class="border border-b-2 border-black"></div>
                        </div>
                    </div>
                </template>
                <div class="flex justify-center">
                    <button class="btn btn-primary" @click="save">
                        Сохранить
                    </button>
                </div>
            </div>
        </div>


    </AuthenticatedLayout>
</template>

<script>
import {Link, router} from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {localizeSection} from "@/utils/localizeSection";

export default {
    name: "Edit",
    components: {
        AuthenticatedLayout,
        Link,
    },
    data() {
        return {
            selectedRoleId: this.manager?.roles && this.manager?.roles.length ? this.manager.roles[0].id :'destroy',
            roles: this.rolesData,
            name: this.$props.manager.name,
            managerData: this.manager,
        }
    },
    props: ['manager', 'sections', 'rolesData'],
    methods: {
        translateSection(name) {
            return localizeSection(name)
        },
        async save() {
            try {
                this.sections.map(section => {
                    if (
                        this.manager.permissions &&
                        this.manager.permissions.length &&
                        this.manager.permissions.find(permission => permission.name.includes(section.name + ' list'))
                    ) {
                        if (section.list_input === false) {
                            section.list_input = false
                        } else {
                            section.list_input = true
                        }
                    }
                    if (
                        this.manager.permissions &&
                        this.manager.permissions.length &&
                        this.manager.permissions.find(permission => permission.name.includes(section.name + ' create'))
                    ) {
                        if (section.create_input === false) {
                            section.create_input = false
                        } else {
                            section.create_input = true
                        }
                    }
                    if (
                        this.manager.permissions &&
                        this.manager.permissions.length &&
                        this.manager.permissions.find(permission => permission.name.includes(section.name + ' edit'))
                    ) {
                        if (section.edit_input === false) {
                            section.edit_input = false
                        } else {
                            section.edit_input = true
                        }
                    }
                    if (
                        this.manager.permissions &&
                        this.manager.permissions.length &&
                        this.manager.permissions.find(permission => permission.name.includes(section.name + ' delete'))
                    ) {
                        if (section.delete_input === false) {
                            section.delete_input = false
                        } else {
                            section.delete_input = true
                        }
                    }
                })
                let data = {
                    name: this.name || null,
                    role_id: this.selectedRoleId === 'destroy' ? null  : this.selectedRoleId,
                    sections: this.sections,
                }
                await axios.patch(`/admin/managers/${this.manager.id}/update`, data)
                router.visit('/admin/managers')
            } catch (e) {
                alert(e.message ?? e)
            }
        },
    },

}
</script>

<style scoped>

</style>
