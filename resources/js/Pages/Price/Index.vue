<template>
    <Spinner v-if="isLoading"/>
    <div class="modal fade" id="createPriceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить цену</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="container-fluid">
                        <div class="row">
                           <div class="col-4">
                               Название
                           </div>
                            <div class="col-8">
                                <input class="form-control" v-model="title"/>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-4">
                                <div>
                                    Выберите группы
                                </div>
                                <small>
                                    У этих клиентов этих групп будет показана эта цена
                                </small>
                            </div>


                            <div class="col-8" v-if="freeGroupsData && freeGroupsData.length">
                                <div class="flex justify-between items-center" v-for="group in freeGroupsData" :key="group.id">
                                    <div>
                                        {{group.title}}
                                    </div>
                                    <input type="checkbox" v-model="createFormGroups" class="form-control" :value="group.id">
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary bg-blue-500" @click="storePrice">Добавить</button>
                </div>
            </div>
        </div>
    </div>

    <AuthenticatedLayout>
        <div class="p-1">
            <button class="btn btn-default">
                <Link href="/admin/products">Назад</Link>
            </button>
        </div>
        <div class="card relative">
            <div class="card-header">
                <div class="row">
                    <div class="col-12">
                        <div class="text-center text-xl">
                            Типы цен
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover text-nowrap" v-if="pricesData && pricesData.length">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Группы</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="price in pricesData" class="cursor-pointer h-[100px]" :title="price.title">
                        <td>{{price.id}}</td>
                        <td>{{price.title}}</td>
                        <td>
                            <template v-if="price.groups && price.groups.length">
                                <div v-for="group in price.groups" :key="group.id">
                                    {{group.title}}
                                </div>
                            </template>
                        </td>
                        <td>
                            <button class="btn btn-danger" @click="deletePrice(price)">
                                Удалить
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div v-else class="row">
                    <div class="col-12">
                        <div class="text-center text-xl">Вы пока не добавляли цены!</div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button
                    class="btn btn-primary bg-blue-500"
                    type="submit"
                    data-toggle="modal" data-target="#createPriceModal"
                    @click="handleCreateClick"
                >
                    Добавить
                </button>
            </div>
        </div>
    </AuthenticatedLayout>

</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Spinner from '@/Components/Spinner.vue'
import {Link} from '@inertiajs/vue3'
export default {
    name: "Index",
    components: {AuthenticatedLayout, Spinner, Link},
    props: ['prices', 'freeGroups'],
    data() {
        return {
            freeGroupsData: this.freeGroups,
            pricesData: this.prices,
            createFormGroups: [],
            isLoading: false,
            title: null,
        }
    },
    methods: {
        handleCreateClick(e) {
            if (!this.freeGroupsData || (this.freeGroupsData && !this.freeGroupsData.length)) {
                e.stopPropagation()
                alert('Добавьте группы')
            }
        },
        async storePrice() {
            if (!this.freeGroupsData || (this.freeGroupsData && !this.freeGroupsData.length))
                return alert('Добавьте группы')
            try {
                this.isLoading = true
                let data = {
                    title: this.title,
                    groups: this.createFormGroups,
                }

                let response = await axios.post('/admin/prices', data)
                this.createFormGroups = []
                this.pricesData.push(response.data)

                this.freeGroupsData = this.freeGroupsData.filter(frGr => !data.groups.includes(frGr.id) )
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                alert(e?.response?.data?.message ?? e?.message ?? e)
            }
        },
        async deletePrice(price) {
            try {
                this.isLoading = true
                await axios.delete(`/admin/prices/${price.id}`)
                this.pricesData = this.pricesData.filter(p => p.id !== price.id)
                this.freeGroupsData = [...this.freeGroupsData, ...price.groups]
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                alert(e?.response?.data?.message ?? e?.message ?? e)
            }
        }
    }
}
</script>

<style scoped>

</style>
