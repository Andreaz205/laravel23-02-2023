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
            <div class="card-header">
                <span class="text-lg mr-2">Группы</span>
                <Link href="/admin/users">
                    <button class="btn btn-default">Клиенты</button>
                </Link>
            </div>
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

    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Link} from "@inertiajs/vue3";
export default {
    name: "Index",
    components: {AuthenticatedLayout, Link},
    props: ['groupsData'],
    data () {
        return {
            newGroup: null,
            groups: this.groupsData
        }
    },
    methods: {
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
    }
}
</script>

<style scoped>

</style>
