<template>
    <div class="form-horizontal">
        <div class="row my-3">
            <div class="col-4">Тип пользователя</div>
            <div class="col-4">
                Физическое лицо
            </div>
            <div class="col-4">
                <button class="btn btn-warning" @click="changeUserKind('organization')">Изменить тип пользователя на
                    организацию
                </button>
            </div>
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
                <div class="col-12">
                    Дополнительные поля
                </div>
            </div>

            <div class="form-group row" v-for="field in fields" :key="field.id">
                <label class="col-sm-2 col-form-label">{{field.title}}</label>
                <div class="col-10">
                    <template v-for="userField in user.fields" :key="userField.id">
                        <input v-if="userField.user_field_id === field.id" class="form-control" type="text" v-model="userField.value" :ref="`field${userField.id}`" :id="userField.id">
                    </template>
                </div>
            </div>
        </template>
        <div class="form-group row">
            <div>
                <button type="submit" class="btn btn-primary bg-blue-500" @click="onSubmit">Сохранить изменения</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ShowSingleUser",
    props: ['user', 'groups', 'changeUserKind', 'fields'],
    data() {
        return {
            name: this.user.name,
            email: this.user.email,
            phone: this.user.phone,
            group_id: this.user.group_id || 'destroy',
            is_subscribed_to_news: this.user.is_subscribed_to_news,
        }
    },
    methods: {
        async onSubmit() {
            try {
                let data = {
                    name: this.name,
                    email: this.email,
                    phone: this.phone,
                    group_id: this.group_id === 'destroy' ? null : this.group_id,
                    is_subscribed_to_news: this.is_subscribed_to_news,
                }
                let fields = Object.keys(this.$refs).map(key => {
                    if (key.includes('field')) return this.$refs[key][0]
                })
                let fieldsData = fields.map(field => {
                    return {
                        user_field_id: field.id,
                        value: field.value,
                    }
                })
                let response = await axios.patch(`/admin/users/${this.user.id}/update`, {...data, fields: fieldsData})
                this.$page.props.user = response.data.data

            } catch (e) {
                alert(e.message ?? e)
            }
        }
    }

}
</script>

<style scoped>

</style>
