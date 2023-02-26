<template>
    <AuthenticatedLayout>
    <div class="card">
        <div class="card-header p-2 flex items-center">
            <Link href="/admin/users" class="btn btn-default mr-3">Назад</Link>
            <ul class="nav nav-pills">
                <li class="nav-item cursor-pointer"><a class="nav-link active" data-toggle="tab">Карточка клиента</a></li>
<!--                <li class="nav-item cursor-pointer"><a class="nav-link"  data-toggle="tab">Группы</a></li>-->
                <Link href="/admin/groups" class="btn btn-default ml-3">Группы</Link>
                <!--                <li class="nav-item"><a class="nav-link" href="#activity" data-toggle="tab">Activity</a></li>-->
            </ul>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" ref="clientCardRef">
                   <ShowSingleUser v-if="user.kind === 'single'" :user="user" :groups="groups" :change-user-kind="changeUserKind"/>
                   <ShowOrganization v-if="user.kind === 'organization'" :user="user" :groups="groups" :change-user-kind="changeUserKind"/>
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
import ShowSingleUser from "@/Pages/User/ShowSingleUser.vue";
import ShowOrganization from "@/Pages/User/ShowOrganization.vue";
export default {
    name: "Show",
    components: {ShowOrganization, ShowSingleUser, AuthenticatedLayout, Link},
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
        // setActive(value) {
        //     if (value === 'clientCard') {
        //         this.$refs.groupsRef.classList.remove('active')
        //         this.$refs.clientCardRef.classList.add('active')
        //     }
        // },

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
