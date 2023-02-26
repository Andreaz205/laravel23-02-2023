<template>
    <AuthenticatedLayout>
        <div class="card card-primary card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                    <li class="pt-2 px-3"><h3 class="card-title"><Link href="/admin/users">Назад</Link></h3></li>
                    <li class="pt-2 px-3"><h3 class="card-title">Добавить пользователя</h3></li>
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-two-home-tab" data-toggle="pill" href="#single" role="tab" aria-controls="single" aria-selected="true">Физическое лицо</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="organization-tab" data-toggle="pill" href="#organization" role="tab" aria-controls="organization" aria-selected="false">Организация</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-two-tabContent">
                    <div class="tab-pane fade show active" id="single" role="tabpanel" aria-labelledby="single-tab">
                        <CreateSingleUserForm :groups="this.groups"/>
                    </div>
                    <div class="tab-pane fade" id="organization" role="tabpanel" aria-labelledby="organization-tab">
                        <CreateOrganizationForm :groups="this.groups" />
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import CreateSingleUserForm from "@/Pages/User/CreateSingleUserForm.vue";
import CreateOrganizationForm from "@/Pages/User/CreateOrganizationForm.vue";
import {Link} from "@inertiajs/vue3";
export default {
    name: "Create",
    components: {CreateOrganizationForm, CreateSingleUserForm, AuthenticatedLayout, Link},
    props: ['groups']

}
</script>

<style scoped>

</style>
