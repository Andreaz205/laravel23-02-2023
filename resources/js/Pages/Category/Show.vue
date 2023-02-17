<template>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Title</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div>{{category.name}}</div>
        <form action="/admin/categories/{{category.id}}/publish" method="POST">
            @csrf
            @method('PUT')
            <div>
                @if(category.is_published)
                <div>Категория опубликована</div>
                <input type="hidden" name="is_published" value=0>
                <button class="btn btn-primary">Скрыть категорию</button>
                @else
                <div>Категория не опубликована</div>
                <input type="hidden" name="is_published" value=1>
                <button class="btn btn-primary">Опубликовать категорию</button>
                @endif
            </div>
        </form>


        <div>
            @if(category.image_url)
            <img src="{{category.image_url}}"/>
            @endif
            <form action="/admin/categories/{{category.id}}/image">

            </form>
        </div>
<!--        &lt;!&ndash; /.card-body &ndash;&gt;-->
<!--      <div class="card-footer">-->
<!--        Footer-->
<!--       </div>-->
<!--      &lt;!&ndash; /.card-footer&ndash;&gt;-->
    </div>
</template>

<script>
export default {
    name: "category",
    props: ['category']
}
</script>

<style scoped>

</style>
