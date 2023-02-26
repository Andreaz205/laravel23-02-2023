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
                <input type="email" class="form-control" id="inputName" placeholder="Имя" :value="user?.name">
            </div>
        </div>
        <div class="form-group row">
            <label for="inputEmail" class="col-sm-2 col-form-label">Почта</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" id="inputEmail" placeholder="Почта" :value="user?.email">
            </div>
        </div>
        <div class="form-group row">
            <label for="phone" class="col-sm-2 col-form-label">Телефон</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="phone" placeholder="Телефон" :value="user?.phone">
            </div>
        </div>

        <div class="form-group row" v-if="groups && groups.length">
            <label>Изменить группу клиента</label>
            <select class="form-control" v-model="group_id">
                <option value="destroy">Вне группы</option>
                <option v-for="group in groups" :value="group.id">{{ group.title }}</option>
            </select>
        </div>

        <div class="form-group row">
            <div class="offset-sm-2 col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" v-model="is_subscribed_to_news"> Подписать на уведомления о заказе
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="offset-sm-2 col-sm-10">
                <button type="submit" class="btn btn-danger bg-red-500" @click="onSubmit">Сохранить</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ShowSingleUser",
    props: ['user', 'groups', 'changeUserKind'],
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
                let response = await axios.patch(`/admin/users/${this.user.id}/update`, data)
            } catch (e) {
                alert(e.message ?? e)
            }
        }
    }

}
</script>

<style scoped>

</style>
