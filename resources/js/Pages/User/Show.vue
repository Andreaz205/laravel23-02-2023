<template>
<!-- TODO: Модальное окно для добавления группы    -->
    <div class="modal fade" id="createGroupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить свойства</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Введите название для группы</label>
                        <input type="text" class="form-control" placeholder="Введите название" v-model="newGroup">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary bg-blue-500" @click="createGroup">Добавить</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <AuthenticatedLayout>
    <div class="card">
        <div class="card-header p-2">
            <ul class="nav nav-pills">
                <li class="nav-item cursor-pointer"><a class="nav-link active" data-toggle="tab" @click="setActive('clientCard')">Карточка клиента</a></li>
                <li class="nav-item cursor-pointer"><a class="nav-link"  data-toggle="tab" @click="setActive('groups')">Группы</a></li>
                <!--                <li class="nav-item"><a class="nav-link" href="#activity" data-toggle="tab">Activity</a></li>-->
            </ul>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" ref="clientCardRef">
                    <form class="form-horizontal">
                        <div class="row my-3">
                            <div class="col-4">Тип пользователя</div>
                            <div class="col-4">
                                {{user.kind === 'single' ? 'Физическое лицо' : 'Организация'}}
                            </div>
                            <div class="col-4">
                                <button class="btn btn-warning" v-if="user.kind === 'single'" @click="changeUserKind('organization')">Изменить тип пользователя на организацию</button>
                                <button class="btn btn-warning" v-else @click="changeUserKind('single')">Изменить тип пользователя на физическое лицо</button>
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
                        <div class="form-group row">
                            <label for="inputExperience" class="col-sm-2 col-form-label">Адрес</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="inputExperience" placeholder="Адрес" :value="user?.physical_address"></textarea>
                            </div>
                        </div>
                        <div class="form-group row" v-if="groups && groups.length">
                            <label>Выберите группу клиента</label>
                            <select class="form-control">
                                <option value="destroy">-</option>
                                <option v-for="group in groups">{{group.title}}</option>
                            </select>
                        </div>
                        <!--                        <div class="form-group row">-->
                        <!--                            <label for="inputSkills" class="col-sm-2 col-form-label">Skills</label>-->
                        <!--                            <div class="col-sm-10">-->
                        <!--                                <input type="text" class="form-control" id="inputSkills" placeholder="Skills">-->
                        <!--                            </div>-->
                        <!--                        </div>-->
                        <div class="form-group row">
                            <div class="offset-sm-2 col-sm-10">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" :checked="user?.order_subscription"> Подписать на уведомления о заказе
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="offset-sm-2 col-sm-10">
                                <button type="submit" class="btn btn-danger bg-red-500">Сохранить</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane" ref="groupsRef">
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                            <tr>
                                <th>Название</th>
                            </tr>
                            </thead>
                            <tbody v-if="groups && groups.length">
                            <tr v-for="group in groups">
                                <td><Link :href="'/admin/groups/' + group.id">{{group.title}}</Link></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary bg-blue"
                                data-toggle="modal" data-target="#createGroupModal">
                            Добавить
                        </button>
                    </div>
                </div>
                <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
        </div><!-- /.card-body -->
    </div>
    </AuthenticatedLayout>
</template>

<script>
import {Link} from '@inertiajs/vue3'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
export default {
    name: "Show",
    components: {AuthenticatedLayout, Link},
    props: [
        'userData',
        'groupsData',
    ],
    data() {
        return {
            user: this.$props.userData,
            groups: this.$props.groupsData,
            newGroup: '',
        }
    },
    methods: {
        setActive(value) {
            if (value === 'clientCard') {
                this.$refs.groupsRef.classList.remove('active')
                this.$refs.clientCardRef.classList.add('active')
            }
            if (value === 'groups') {
                this.$refs.clientCardRef.classList.remove('active')
                this.$refs.groupsRef.classList.add('active')
            }
        },
        async createGroup() {
            try {
                let data = {
                    title: this.newGroup,
                }
                let response = await axios.post('/admin/groups', data)
                this.groups.push(response.data)
            } catch (e) {
                alert(e)
            }
        },
        async changeUserKind(kind) {
            try {
                let response = await axios.patch(`/admin/users/${this.user.id}/kind`, {kind: kind})
                this.user = response.data.data
            } catch (e) {
                alert(e?.message ?? e)
            }
        }
    },

    mounted() {
        console.log(this.user)
        console.log(this.groups)
    }
}
</script>

<style scoped>

</style>
