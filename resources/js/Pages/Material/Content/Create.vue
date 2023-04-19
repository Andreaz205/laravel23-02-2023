<template>
    <div class="card">
        <div class="card-header text-center text-lg relative">
            Создать контент для вариантов
            <button class="btn btn-default absolute top-1/2 -translate-y-1/2 left-1">
                <Link :href="`/admin/materials`">
                    К материалам
                </Link>
            </button>
        </div>
    </div>

    <FlashMessage />

    <div class="card m-3">
        <div class="card-body">
            <div class="form-group">
                <div class="row">
                    <div class="col-3">
                        <label for="">Укажите название:
                            <span class="text-red">
                                *
                            </span>
                        </label>
                    </div>
                    <input type="text" class="form-control col-9" v-model="title">
                </div>
            </div>
        </div>
    </div>

    <div class="card m-3" v-if="materials?.length">
        <div class="card-header text-center text-lg">
            Укажите значения которым будет соответствовать контент
            <span class="text-red">
                *
            </span>
        </div>
        <div class="card-body">
            <template v-for="(materialItem, idx) in materials" :key="idx">
                <div class="row">
                    <div class="col-12 text-center text-lg">
                        {{materialItem.material}}
                    </div>
                </div>

                <div class="row">
                    <div v-for="material_unit_value in materialItem.material_unit_values" :key="material_unit_value.id" class="col-12 flex justify-between">
                        <div>
                            {{material_unit_value.value}}
                        </div>
                        <input type="checkbox" class="form-control" v-model="material_unit_value_ids" :value="material_unit_value.id">
                    </div>
                </div>
            </template>
        </div>

    </div>

    <div class="row m-2" v-if="materials?.length">
        <div class="col-12">
            <button class="btn btn-primary w-full" @click="storeContent">
                Создать
            </button>
        </div>
    </div>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Link} from '@inertiajs/vue3'
import FlashMessage from "@/Components/FlashMessage.vue";
export default {
    name: "Create",
    components: {FlashMessage, Link},
    layout: AuthenticatedLayout,
    props: [
        'materials'
    ],
    data(){
        return {
            isLoading: false,
            errors: null,
            title: null,
            material_unit_value_ids: [],
        }
    },
    methods: {
        async storeContent() {
            try {
                this.isLoading = true
                let data = {
                    title: this.title,
                    material_unit_value_ids: this.material_unit_value_ids
                }
                await this.$inertia.post(`/admin/materials/variants-content`, data)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
            }
        }
    }
}
</script>

<style scoped>

</style>
