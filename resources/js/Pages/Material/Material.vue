<template>
    <!--    TODO: Модальное окно для добавления значений-->
    <div class="modal fade" id="createUnitModal" tabindex="-1" role="dialog" aria-labelledby="optionNameColorModal"
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
                            <label class="col-4">Название элемента</label>
                            <div class="col-8">
                                <input type="text" class="form-control" v-model="newUnit">
                            </div>
                        </div>
                        <div class="row" v-if="selectedUnit">
                            <div class="col-12">
                                <h1 class="text-lg underline">Для элемента {{selectedUnit.title}} <i class="fas fa-times cursor-pointer" @click="selectedUnit = null"></i></h1>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть</button>
                    <button
                        class="btn btn-primary"
                        @click="createUnit"
                    >
                        Добавить
                    </button>
                    <!--                        <button type="button" class="btn btn-primary bg-blue-500" @click="saveOption">Сохранить-->
                    <!--                        </button>-->
                </div>
            </div>
        </div>
    </div>

    <Spinner v-if="isLoading"/>
    <AuthenticatedLayout>
        <FlashMessage />
        <div class="card">
            <div class="card-header text-center text-xl relative">
                {{material.title}}
                <div class="absolute top-1/2 left-3 -translate-y-1/2">
                    <button class="btn btn-default">
                        <Link href="/admin/materials">
                            Назад
                        </Link>
                    </button>
                    <button
                        v-if="!material.material_unit"
                        type="button" class="btn btn-primary bg-blue ml-3"
                        data-toggle="modal" data-target="#createUnitModal"
                    >
                        Добавить
                    </button>

                    <button class="btn btn-danger ml-2" @click="deleteMaterial">
                        Удалить
                    </button>
                </div>
            </div>
        </div>
        <div class="p-4">
            <Link :href="`/admin/materials/${this.material.id}/colors`">
                <button class="btn btn-primary w-full py-5">
                        Указать цвета
                </button>
            </Link>
        </div>
        <div class="mt-5 container-fluid" v-if="material.material_unit">

            <div class="flex gap-5 flex-nowrap">
                <template v-for="(unit) in plainUnits">
                    <div class="card w-[500px] m-4 relative">
                    <div class="card-header">
                        {{unit.title}}
                        <button class="btn btn-danger" @click="deleteUnit(unit)">
                            Удалить элемент
                        </button>
                        <Link class="ml-2" :href="`/admin/materials/units/${unit.id}`">
                            <button class="btn btn-warning ">
                                Редактировать
                            </button>
                        </Link>
                    </div>
<!--                    <div class="card-header">-->
<!--                        <div v-if="!unit.parent_material_unit_id">-->
<!--                            {{selectedMainValue?.value}}-->
<!--                        </div>-->
<!--                        <div v-else>-->

<!--                        </div>-->
<!--                    </div>-->
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <template v-for="value in unit.values">
                                <template v-if="unit.parent_material_unit_id">

                                </template>
                                <template v-if="!unit.parent || value.isVisible">
                                    <li :class="['list-group-item cursor-pointer', {'bg-gray-100': value.active}]" @click="handleItemClick(unit, value)">
                                        {{value.value}}
<!--                                        <button class="btn btn-danger" @click="deleteUnit(unit)">-->
<!--                                            Удалить значение-->
<!--                                        </button>-->
                                    </li>
                                </template>
                            </template>
                        </ul>
                        <!--   <div class="card-text">-->
                        <!--                                        <div>{{ unit.id }}</div>-->
                        <!--                                        <div>-->
                        <!--                                            {{ unit.title }}-->
                        <!--                                        </div>-->
                        <!--                                        <div class="d-flex justify-start">-->
                        <!--                                            &lt;!&ndash;                            <Link :href="'/admin/managers/' + manager.id + '/edit'" class="mr-2" v-if="canManagers.edit && user.id !== manager.id">&ndash;&gt;-->
                        <!--                                            &lt;!&ndash;                                <i class="fas fa-edit "></i>&ndash;&gt;-->
                        <!--                                            &lt;!&ndash;                            </Link>&ndash;&gt;-->
                        <!--                                            &lt;!&ndash;                            <a @click="deleteManager(manager)" v-if="canManagers.delete && user.id !== manager.id">&ndash;&gt;-->
                        <!--                                            &lt;!&ndash;                                <i class="fas fa-times"></i>&ndash;&gt;-->
                        <!--                                            &lt;!&ndash;                            </a>&ndash;&gt;-->
                        <!--                                            &lt;!&ndash;                            <div v-if="user.id === manager.id">&ndash;&gt;-->
                        <!--                                            &lt;!&ndash;                                Вы&ndash;&gt;-->
                        <!--                                            &lt;!&ndash;                            </div>&ndash;&gt;-->
                        <!--                                            <button class="btn btn-danger" @click="deleteUnit(unit)">-->
                        <!--                                                Удалить элемент-->
                        <!--                                            </button>-->
                        <!--                                        </div>-->
                        <!--                                    </div>-->
                    </div>
                    <div class="absolute top-1/2 -translate-y-1/2 right-0 translate-x-[120%] hover:text-gray-400 transition-all">
                        <i class="fas fa-long-arrow-alt-right text-xl cursor-pointer"
                           type="button"
                           data-toggle="modal"
                           data-target="#createUnitModal"
                           @click="handleArrowClick($event, unit)"
                        ></i>
                    </div>
                </div>
                </template>

            </div>

        </div>

        <div v-else class="card">
            <div class="card-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 text-center p-5 text-xl">
                            Для данного свойства ещё не создавали элементов!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import {Link} from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import FlashMessage from "@/Components/FlashMessage.vue";
import Spinner from "@/Components/Spinner.vue";
import Errors from "@/Components/Errors/Errors.vue";
            // selectedMainValue: this.material.material_unit && this.material.material_unit.values ? this.material.material_unit.values[0] : null

export default {
    name: "Option",
    components: { Errors, Spinner, FlashMessage, AuthenticatedLayout, Link},
    props: ['material_units', 'material'],
    data () {
        return {
            plainUnits: [...this.plainUnitsFunction(this.material.material_unit)],
            newUnit: '',
            errors: null,
            isLoading: false,
            selectedUnit: null,
        }
    },
    methods: {
        async deleteMaterial() {
            try {
                this.isLoading = true
                await this.$inertia.delete(`/admin/materials/${this.material.id}`)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                alert(e?.message ?? e)
            }
         },
        async deleteUnit(unit) {
            try {
                this.isLoading = true
                await axios.delete(`/admin/materials/units/${unit.id}`)
                location.reload()
                // this.material.material_unit.splice(this.material.material_units.indexOf(unit), 1)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                alert(e?.message ?? e)
            }
        },
        async createUnit() {
            try {
                this.isLoading = true
                let data = {
                    title: this.newUnit,
                    parent_material_unit_id: this.selectedUnit?.id
                }
                await axios.post(`/admin/materials/${this.material.id}/units`, data)
                location.reload()
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                alert(e?.message ?? e)
            }
        },
        handleArrowClick(event, unit) {
            if (unit.child_unit) return event.stopPropagation()
            this.selectedUnit = unit
        },
        plainUnitsFunction(mainUnit) {
            let units = []
            let currentUnit = mainUnit
            units.push(currentUnit)
            while (currentUnit?.child_unit) {
                currentUnit = currentUnit.child_unit
                units.push(currentUnit)
            }
            return units
        },
        handleItemClick(u, value) {
            let activeParentValue = null
            let foundFlag = false
            this.plainUnits?.map((unit, idx) => {
                if (u.id === unit.id) {
                    unit.values.map(val => {
                        if (val.id === value.id) {
                            val.active = true
                            activeParentValue = val
                        } else {
                            val.active = false
                        }
                    })
                    return foundFlag = true
                }
                if (foundFlag) {
                    let previousUnit = this.plainUnits[idx - 1]
                    activeParentValue = previousUnit?.values?.find(val => val.active)
                    let activeFlag = true
                    unit?.values?.map((value, index) => {
                        value.isVisible = value.parent_material_unit_value_id === activeParentValue?.id
                        if (activeFlag && value.isVisible) {
                            value.active = true
                            activeFlag = false
                        } else {
                            value.active = false
                        }
                    })
                }
            })
        }
    },
    mounted() {
        let activeParentValue = null
        this.plainUnits?.map((unit, idx) => {
            if (idx === 0 && unit?.values && unit?.values[0]) {
                activeParentValue = unit.values[0]
                if (this.plainUnits.length >= idx + 1) {
                    let searchedChildUnit = this.plainUnits[idx + 1]
                    searchedChildUnit?.values.map(value => {
                        value.isVisible = value.parent_material_unit_value_id === activeParentValue.id;
                    })
                }
                return unit.values[0].active = true
            }
            unit?.values?.map(value => {
                if (value.parent_material_unit_value_id === activeParentValue.id) {
                    activeParentValue = value
                    return value.active = true
                }
            })
            if (this.plainUnits.length >= idx + 1) {
                this.plainUnits[idx + 1]?.values?.map(value => {
                    value.isVisible = value.parent_material_unit_value_id === activeParentValue.id;
                })
            }
        })
    }

}
</script>

<style scoped>

</style>
