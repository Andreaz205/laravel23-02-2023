<template>
    <Spinner v-if="isLoading"/>
    <!--    TODO: Модальное окно для добавления значений-->
    <div class="modal fade" id="createValueModal" tabindex="-1" role="dialog" aria-labelledby="optionNameColorModal"
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
                    <Errors :errors="errors" />
                    <div class="container-fluid">
                        <div class="row">
                            <label class="col-4">Значение для элемента(колонки){{material_unit.title}}</label>
                            <div class="col-8">
                                <input type="text" class="form-control" v-model="value">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <label class="col-4">Вышестоящее родительское значение:</label>
                            <div class="col-8">
                                <strong>{{selectedParentValue?.value}}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть</button>
                    <button
                        class="btn btn-primary"
                        @click="createValue"
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
        <FlashMessage />
        <div class="row">
            <div class="col-4" v-if="parent_unit && parent_unit">
                <div class="card m-3">
                    <div class="card-header text-lg">
                        Выберите соответствующее значение слева
                    </div>
                    <div class="card-body">
                        <template v-if="parent_values && parent_values.length">
                            <ul class="list-group list-group-flush">
<!--                                <template v-for="value in unit.values">-->
<!--                                    <template v-if="unit.parent_material_unit_id">-->
                                    <li v-for="value in parent_values"
                                        @click="selectedParentValue = value"
                                        :key="value.id"
                                        :class="['list-group-item cursor-pointer',
                                         {'bg-gray-100': selectedParentValue?.id == value.id}
                                         ]"
                                    >
<!--                                        @click="handleItemClick(value)"-->
                                        {{value.value}}
                                        <!--                                        <button class="btn btn-danger" @click="deleteUnit(unit)">-->
                                        <!--                                            Удалить значение-->
                                        <!--                                        </button>-->
                                    </li>
<!--                                    </template>-->
<!--                                    <template v-if="!unit.parent_material_unit_id">-->

<!--                                    </template>-->
<!--                                </template>-->
                            </ul>
                        </template>
                        <div v-else>
                            Необходимо добавить значения для элемента {{parent_unit.title}}
                            <button class="btn btn-primary">
                                <Link :href="`/admin/materials/units/${parent_unit.id}`">
                                    Перейти
                                </Link>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-8">
                <div class="card m-3">
                    <div class="card-header text-center text-xl relative">
                        Значения для элемента {{material_unit.title}}
                        <div class="absolute left-2 top-1/2 -translate-y-1/2">
                            <button class="btn btn-default">
                                <Link :href="`/admin/materials/${material_unit.material.id}`">
                                    Назад
                                </Link>
                            </button>
                            <button
                                type="button" class="btn btn-primary bg-blue ml-2"
                                data-toggle="modal" data-target="#createValueModal"
                            >
                                Добавить
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table text-nowrap" v-if="material_unit.values && material_unit.values.length">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Название</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-for="value in material_unit.values" :key="value.id">
                                <tr v-if="value.parent_material_unit_value_id == selectedParentValue?.id || !value.parent_material_unit_value_id">
                                    <td>{{ value.id }}</td>
                                    <td>
                                        {{ value.value }}
                                    </td>
                                    <td class="d-flex justify-start">
                                        <button class="btn btn-danger" @click="deleteValue(value)">
                                            Удалить
                                        </button>
                                        <!--                            <Link :href="'/admin/managers/' + manager.id + '/edit'" class="mr-2" v-if="canManagers.edit && user.id !== manager.id">-->
                                        <!--                                <i class="fas fa-edit "></i>-->
                                        <!--                            </Link>-->
                                        <!--                            <a @click="deleteManager(manager)" v-if="canManagers.delete && user.id !== manager.id">-->
                                        <!--                                <i class="fas fa-times"></i>-->
                                        <!--                            </a>-->
                                        <!--                            <div v-if="user.id === manager.id">-->
                                        <!--                                Вы-->
                                        <!--                            </div>-->
                                    </td>
                                </tr>
                            </template>

                            </tbody>

                        </table>
                        <div v-else class="p-5 text-center text-lg">
                            Для элемента {{material_unit.title}} нет значений!
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </AuthenticatedLayout>

</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Errors from '@/Components/Errors/Errors.vue'
import {Link} from '@inertiajs/vue3'
import Spinner from "@/Components/Spinner.vue";
import FlashMessage from "@/Components/FlashMessage.vue";
export default {
    name: "Unit",
    components: {FlashMessage, Spinner, AuthenticatedLayout, Link, Errors},
    props: ['material_unit', 'parent_values', 'parent_unit'],
    data () {
        return {
            selectedParentValue: this.parent_values ? this.parent_values[0] : null,
            value: '',
            isLoading: false,
            errors: null,
        }
    },
    methods: {
        async createValue() {
            try {
                this.isLoading = true
                let data = {
                    value: this.value,
                    parent_material_unit_value_id: this.selectedParentValue?.id
                }
                let response = await axios.post(`/admin/materials/units/${this.material_unit.id}/values`, data)
                this.material_unit.values.push(response.data)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                alert(e?.message ?? e)
            }
        },
        async deleteValue(value) {
            try {
                this.isLoading = true
                await this.$inertia.delete(`/admin/materials/values/${value.id}`)
                let index = this.material_unit.values?.indexOf(value)
                this.material_unit.values.splice(index, 1)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                alert(e?.message ?? e)
            }
        }
    }
}
</script>

<style scoped>

</style>
