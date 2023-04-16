<template>
    <Spinner v-if="isLoading"/>
    <Errors :errors="errors" />
    <div class="form-horizontal">
        <div class="row my-3">
            <div class="col-4">Тип пользователя</div>
            <div class="col-4">
                Физическое лицо
            </div>
<!--            <div class="col-4">-->
<!--                <button class="btn btn-warning" @click="changeUserKind('organization')">Изменить тип пользователя на-->
<!--                    организацию-->
<!--                </button>-->
<!--            </div>-->
        </div>
        <div class="form-group row">
            <label for="inputName" class="col-sm-2 col-form-label">Имя</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" id="inputName" placeholder="Имя" v-model="name">
            </div>
        </div>
        <div class="form-group row">
            <label for="inputEmail" class="col-sm-2 col-form-label">Почта</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" id="inputEmail" placeholder="Почта" v-model="email">
            </div>
        </div>
        <div class="form-group row">
            <label for="phone" class="col-sm-2 col-form-label">Телефон</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="phone" placeholder="Телефон" v-model="phone">
            </div>
        </div>

        <div class="form-group row" v-if="groups && groups.length">
            <label class="col-sm-2 col-form-label">Изменить группу клиента</label>
            <div class="col-sm-10">
                <select class="form-control" v-model="group_id">
                    <option value="destroy">Вне группы</option>
                    <option v-for="group in groups" :value="group.id">{{ group.title }}</option>
                </select>
            </div>
        </div>

        <div class="form-group row">
                <label for="inputName" class="col-sm-2 col-form-label">Подписать на уведомления о заказе</label>
                <div class="checkbox">
                    <div class="col-sm-10">
                        <input type="checkbox" v-model="is_subscribed_to_news">
                    </div>
                </div>

        </div>

        <template v-if="fields && fields.length">
            <div class="row">
                <div class="col-12 text-center text-lg mt-2 mb-3">
                    Дополнительные поля
                </div>
            </div>

            <div class="form-group row" v-for="field in fields" :key="field.id">
                <label class="col-sm-2 col-form-label">{{field.title}}</label>
                <div class="col-10">
                    <template v-for="userField in user.fields" :key="userField.id">
                        <template v-if="+userField.user_field_id === +field.id">
                            <input v-if="field.type === 'string'"  class="form-control" type="text" :value="userField.value" :ref="`field-${userField.id}`" :id="userField.id">
                            <textarea v-if="field.type === 'text'"  class="form-control"  :value="userField.value" :ref="`field-${userField.id}`" :id="userField.id" />
                            <input v-if="field.type === 'bool'"  class="form-control" type="checkbox" :checked="userField.value" :ref="`field-${userField.id}`" :id="userField.id">
                            <VueDatePicker v-if="field.type === 'date'" v-model="userField.value" @update:model-value="handleDatePickerSelect($event, userField.id)"  placeholder="Укажите дату ..." text-input :ref="`field-${userField.id}`" :id="userField.id"/>
                        </template>
                    </template>
                </div>
            </div>
        </template>

            <div class="flex justify-center">
                <button class="btn btn-primary" @click="onSubmit">Сохранить изменения</button>
            </div>

    </div>
</template>

<script>
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import Spinner from "@/Components/Spinner.vue";
import Errors from "@/Components/Errors/Errors.vue";
export default {
    name: "ShowSingleUser",
    props: ['user', 'groups', 'changeUserKind', 'fields'],
    components: {Errors, Spinner, VueDatePicker},
    data() {
        return {
            errors: null,
            isLoading: false,
            name: this.user.name,
            email: this.user.email,
            phone: this.user.phone,
            group_id: this.user.group_id || 'destroy',
            is_subscribed_to_news: this.user.is_subscribed_to_news,
        }
    },
    methods: {
        handleDatePickerSelect(value, userFieldId) {
            value = JSON.parse(JSON.stringify(value))
            this.$refs[`field-${userFieldId}`][0].date_value = value
        },
        async onSubmit() {
            try {
                this.isLoading = true
                let data = {
                    name: this.name,
                    email: this.email,
                    phone: this.phone,
                    group_id: this.group_id === 'destroy' ? null : this.group_id,
                    is_subscribed_to_news: this.is_subscribed_to_news,
                }

                let fields = Object.keys(this.$refs).map(key => {
                    if (key.includes('field')) {
                        return this.$refs[key][0]
                    }
                })

                let fieldsData = fields.map(field => ({
                        user_field_id: field.id,
                        value: field?.type === 'checkbox' ? field.checked : field.__vueParentComponent ? field.value : field.date_value,
                    })
                )

                let response = await axios.patch(`/admin/users/${this.user.id}/update`, {...data, fields: fieldsData})
                this.$page.props.user = response.data.data

                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                if (e?.response?.status === 500) return this.errors = [e.response.message]
                alert(e.message ?? e)
            }
        }

    },

    mounted() {
        let keys = Object.keys(this.$refs).filter(key => key.includes('field'))
        keys.map(key => {
            let field = this.$refs[key][0]
            if (!field.__vueParentComponent) {
                let fieldId = +key.split('-')[1]
                let candidateUserField = this.user.fields?.find(f => f.id === +fieldId)
                if (candidateUserField) {
                    field.id = candidateUserField.id
                    field.date_value = candidateUserField.value
                }
            }
        })
    }

}
</script>

<style scoped>

</style>
