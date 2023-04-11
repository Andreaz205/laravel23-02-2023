<template>
    <Spinner v-if="isLoading" />
<!--    <div class="modal fade" id="createFieldModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"-->
<!--         aria-hidden="true">-->
<!--        <div class="modal-dialog modal-xl" role="document">-->
<!--            <div class="modal-content">-->
<!--                <div class="modal-header">-->
<!--                    <h5 class="modal-title" id="exampleModalLabel">Добавить свойства</h5>-->
<!--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
<!--                        <span aria-hidden="true">&times;</span>-->
<!--                    </button>-->
<!--                </div>-->
<!--                <div class="modal-body">-->
<!--                    <div class="container-fluid">-->

<!--                        <Errors :errors="errors"/>-->

<!--                        <div class="row">-->
<!--                            <div class="col-4">-->
<!--                                <label>Название</label>-->
<!--                            </div>-->
<!--                            <div class="col-8">-->
<!--                                <input type="text" class="form-control" v-model="title" placeholder="Название поля">-->
<!--                            </div>-->
<!--                        </div>-->

<!--                        <div class="row">-->
<!--                            <div class="col-4">-->
<!--                                <label>Обязательное</label>-->
<!--                            </div>-->
<!--                            <div class="col-8">-->
<!--                                <input type="checkbox" class="form-control" v-model="is_required">-->
<!--                            </div>-->
<!--                        </div>-->

<!--                        <div class="row">-->
<!--                            <div class="col-4">-->
<!--                                <label>Заполняется клиентом</label>-->
<!--                            </div>-->
<!--                            <div class="col-8">-->
<!--                                <input type="checkbox" class="form-control" v-model="is_user_fill">-->
<!--                            </div>-->
<!--                        </div>-->

<!--                        <div class="row ">-->
<!--                            <div class="col-4">-->
<!--                                <label>Тип</label>-->
<!--                            </div>-->
<!--                            <div class="col-8">-->
<!--                                <select class="form-control" v-model="type">-->
<!--                                    <option value="string">Строковое</option>-->
<!--                                    <option value="text">Несколько строк</option>-->
<!--                                    <option value="date">Дата</option>-->
<!--                                    <option value="bool">Да/Нет(Checkbox)</option>-->
<!--                                </select>-->
<!--                            </div>-->
<!--                        </div>-->

<!--                        <div class="row mt-2">-->
<!--                            <div class="col-4">-->
<!--                                <label>Описание</label>-->
<!--                            </div>-->
<!--                            <div class="col-8">-->
<!--                                <textarea  class="form-control" v-model="description" />-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="modal-footer">-->
<!--                    <button type="button" class="btn btn-secondary bg-gray-500" ref="close" data-dismiss="modal">Закрыть-->
<!--                    </button>-->
<!--                    <button type="button" class="btn btn-primary bg-blue-500" @click="addField">Сохранить-->
<!--                    </button>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->

    <AuthenticatedLayout>
        <div class="card">
            <div class="card-header text-xl text-center">
                Настройки офрмления заказов
            </div>

            <div class="card-body">
                <div class="col-12 text-lg">
                    Настройка полей заказа
                </div>
                <div class="col-12">
                    <div class="container-fluid table-responsive">

                        <button
                            class="btn btn-default my-1"
                            type="button"
                            data-toggle="modal"
                            data-target="#createFieldModal"
                        >
                            Добавить
                        </button>

                        <table class="table text-nowrap border-x-2 border-b-2">
                            <thead>
                            <tr>
                                <th>Название</th>
                                <th>Тип</th>
                                <th>Обязательное</th>
                                <th>Заполняется клиентом</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody v-if="fields && fields.length">
                            <tr v-for="field in fields">
                                <td>{{ field.title }}</td>
                                <td>{{ field.type === 'string' ? 'Строковое' : field.type === 'text' ? 'Несколько строк' ? field.type === 'date' : 'Дата' : 'Логическое Да/Нет(Checkbox)' }}</td>
                                <td>{{ field.is_user_fill ? 'Да' : 'Нет' }}</td>
                                <td>{{ field.is_required ? 'Да' : 'Нет' }}</td>
                                <td>
                                    <i class="fas cursor-pointer fa-times" @click="deleteField(field)"></i>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Errors from '@/Components/Errors/Errors.vue'
import Spinner from "@/Components/Spinner.vue";
export default {
    name: "Index",
    components: {Spinner, AuthenticatedLayout, Errors},
    props: ['fields'],
    data () {
        return {
            isLoading: false,
            errors: null,
            title: null,
            is_required: true,
            is_user_fill: true,
            type: 'string',
            description: null,
        }
    },
    methods: {
         async addField() {
             try {
                 this.isLoading = true
                 let data = {
                     title: this.title,
                     is_required: this.is_required,
                     is_user_fill: this.is_user_fill,
                     type: this.type,
                     description: this.description
                 }
                 let response = await axios.post(`/admin/orders/fields`, data)
                 this.fields.push(response.data)
                 this.errors = null
                 this.$refs.close.click()
                 this.isLoading = false
             } catch (e) {
                 this.isLoading = false
                 if (e?.response?.status === 422) {
                     return this.errors = e.response.data.errors
                 }
                 alert(e.message)
             }
         },
        async deleteField(field) {
            try {
                this.isLoading = true
                await axios.delete(`/admin/orders/fields/${field.id}`)
                this.fields.splice(this.fields.indexOf(field), 1)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                alert(e.message ?? e)
            }
        }
    }
}
</script>

<style scoped>

</style>
