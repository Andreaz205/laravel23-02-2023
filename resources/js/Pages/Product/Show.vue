<template>
    <AuthenticatedLayout>

        <!--    TODO: Модальное окно для редактирования свойств-->
        <div class="modal fade" id="createOptionsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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
                        <template v-if="createOptionFormData && createOptionFormData.length">
                            <div class="row" v-for="option in createOptionFormData">
                                <div
                                    :class="[{'col-sm-6': option.id == createOptionFormData[0].id}, {'col-sm-5': option.id != createOptionFormData[0].id}]">
                                    <!-- text input -->
                                    <div class="form-group">
                                        <label>Название свойства</label>
                                        <input type="text" v-model="option.name" class="form-control"
                                               placeholder="Введите название свойства">
                                    </div>
                                </div>
                                <div
                                    :class="[{'col-sm-6': option.id == createOptionFormData[0].id}, {'col-sm-5': option.id != createOptionFormData[0].id}]">
                                    <div class="form-group">
                                        <label>Значение по умолчанию</label>
                                        <input type="text" class="form-control" v-model="option.defaultValue"
                                               placeholder="Введите значение по умолчанию">
                                    </div>
                                </div>
                                <div class="col-sm-2 d-flex justify-center items-end"
                                     v-if="option.id != createOptionFormData[0].id">
                                    <div class="form-group">
                                        <button class="btn btn-danger"
                                                @click="deleteOptionInCreateForm($event, option.id)">
                                            Удалить
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-center">
                                <button class="btn btn-info" @click="addOptionNameInForm">
                                    Добавить свойство
                                </button>
                            </div>
                        </template>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть
                        </button>
                        <button type="button" class="btn btn-primary bg-blue-500" @click="createOptionFetch">Сохранить
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!--    TODO: Модальное окно для редактирования свойства в целом-->
        <div class="modal fade" id="editOptionModal" tabindex="-1" role="dialog" aria-labelledby="optionNameColorModal"
             aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Редактировать свойство</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Отображать свойтсво как цвет товара</label>
                            <CustomSwitch :is-checked="selectedOptionName?.is_color" :switch-id="'option-name-' + selectedOptionName?.id" @changeSwitch="onChangeOptionColor"/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть
                        </button>
<!--                        <button type="button" class="btn btn-primary bg-blue-500" @click="saveOption">Сохранить-->
<!--                        </button>-->
                    </div>
                </div>
            </div>
        </div>

        <!--    TODO: Модальное окно для создания варианта-->
        <div class="modal fade" id="createVariantModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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

                        <template v-if="creatingVariantFormData && creatingVariantFormData.length">
                            <div class="container-fluid" v-for="dataItem in creatingVariantFormData">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ dataItem.title }}</label>
                                            <select class="custom-select"
                                                    @change="handleClickCreateVariantOptionValue($event, dataItem)"
                                                    :value="dataItem.creating_variant_selected_id">
                                                <option value="new">Добавить новое значение</option>
                                                <option v-for="(value) in dataItem.option_values" :value="value.id">
                                                    {{ value.title }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="dataItem.is_new" class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <input type="text" v-model="dataItem.new_value" class="form-control"
                                                   placeholder="Введите название свойства">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template v-else>
                            <div class="container">
                                <div class="row">
                                    <div class="col-xl-12 mb-2">
                                        <button class="btn btn-primary" @click="addVariantCreatingOption">
                                            Добавить свойство
                                        </button>
                                    </div>
                                </div>
                                <div class="row" v-for="option in variantCreatingOptions">
                                    <div class="col-5">
                                        <div class="form-group">
                                            <label>Название свойства</label>
                                            <input type="text" class="form-control" v-model="option.name"
                                                   placeholder="Введите название свойства">
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <div class="form-group">
                                            <label>Значение по умолчанию</label>
                                            <input type="text" class="form-control" v-model="option.value"
                                                   placeholder="Введите значение по умолчанию">
                                        </div>
                                    </div>
                                    <div class="col-xl-2 flex justify-center items-center" v-if="option.id != 1">
                                        <button class="btn btn-danger" @click="deleteVariantCreatingOption(option.id)">
                                            Удалить
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть
                        </button>
                        <button type="button" class="btn btn-primary bg-blue-500" @click="createVariant">Добавить
                            вариант
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!--    TODO: Модальное окно для редактирования свойств варианта-->
        <div class="modal fade" id="changeVariantOptionsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Редактировать свойства</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <template v-if="selectedVariant">
                            <div class="container-fluid" v-for="option_name in product.option_names">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ option_name.title }}</label>
                                            <select class="custom-select"
                                                    @change="handleClickChangeVariantOptionValue($event, option_name)"
                                                    :value="selectedVariant.option_values.find(value => value.option_name_id == option_name.id)?.id">
                                                <option value="new">Добавить новое значение</option>
                                                <option v-for="(value) in option_name.option_values" :value="value.id">
                                                    {{ value.title }}
                                                </option>
                                            </select>
                                            <form v-if="option_name.edit_form_data_is_new" @submit="onSubmitEditOptionsFormNewValue($event, option_name)">
                                                <div class="form-group" >
                                                    <label>Введите значение</label>
                                                    <input class="form-control" type="text" placeholder="Введите значение">
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-primary bg-blue" type="submit">Сохранить</button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!--    TODO: Модальное окно для удаления существующих свойств-->
        <div class="modal fade" id="deleteOptionsModal" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Удалить свойства</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="container-fluid" v-if="product.option_names">
                            <div class="row my-2" v-for="name in product.option_names">
                                <div class="col-sm-6">
                                    <span>{{ name.title }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <button class="btn btn-danger" @click="deleteOption(name.id)">Удалить</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!--    TODO: Модальное окно для привязки изображений варианту-->
        <div class="modal fade" id="bindImagesToVariant" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <form class="modal-content" @submit="onSubmitImageForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Фотографии варианта</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <ModalImages :product="product" :selected-variant-images="selectedVariant"/>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть
                        </button>
                        <button type="submit" class="btn btn-primary bg-blue">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Content Header (Page header) -->
        <div>

            <div class="mx-5 flex justify-between py-4">
                <h1 class="text-xl">{{ product.title }}</h1>
                <Link href="/">Главная</Link>
            </div>

<!--                <div class="container-fluid">-->
<!--                    <div class="row mb-2">-->
<!--                        <div class="col-sm-6">-->
<!--            -->
<!--                        </div>&lt;!&ndash; /.col &ndash;&gt;-->
<!--                        <div class="col-sm-6">-->
<!--                            <ol class="breadcrumb float-sm-right">-->
<!--                                <li class="breadcrumb-item">-->

<!--                                </li>-->
<!--                            </ol>-->
<!--                        </div>&lt;!&ndash; /.col &ndash;&gt;-->
<!--                    </div>&lt;!&ndash; /.row &ndash;&gt;-->
<!--                </div>&lt;!&ndash; /.container-fluid &ndash;&gt;-->
<!--            </div>-->
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content mx-5">

                <div class="row">
                    <div class="col-9">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header text-center">
                                        <div class="p-2 text-xl">Информация о товаре</div>
                                    </div>
                                    <div class="card-body">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="flex justify-between items-center">
                                                        <Link :href="`/admin/products/${this.product.id}/edit`">
                                                            <button class="btn btn-warning">
                                                                Редактировать <i class="fas fa-edit"></i>
                                                            </button>
                                                        </Link>
                                                        <button
                                                            v-if="canProducts.delete"
                                                            class="btn btn-danger"
                                                            value="Удалить"
                                                            @click="deleteProduct"
                                                        >
                                                            Удалить товар
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <span class="text-bold">Применять скидки: </span>
                                                    <span>{{ product.allow_sales ? 'Да' : ' Нет' }}</span>
                                                </div>
                                            </div>

                                            <template v-if="product.parameters && product.parameters.length">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="text-bold">Параметры:</div>
                                                    </div>
                                                </div>

                                                <div class="row  ">
                                                    <div v-for="parameter in product.parameters" class="ml-3 col-12">
                                                        <span>{{ parameter.title }}: </span>
                                                        <span>{{ parameter.value }}</span>
                                                    </div>
                                                </div>
                                            </template>

                                            <div class="row">
                                                <div class="col-12 text-bold">
                                                    Размеры:
                                                </div>
                                            </div>

                                            <div class="row  ">
                                                <div class="col-12 pl-4">
                                                    <div v-if="product.length">Длина - {{product.length}}</div>
                                                    <div v-if="product.width">Ширина - {{product.width}}</div>
                                                    <div v-if="product.height">Высота - {{product.height}}</div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 text-bold">
                                                    Описание
                                                </div>
                                            </div>

                                            <div class="row my-2">
                                                <div class="col-12">
                                                    {{product.description}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header text-center text-xl">
                                        <div class="p-2">Фотографии товара</div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-8">
                                                <DraggableProductImages
                                                    ref="draggable"
                                                    @deleteImage="deleteImage"
                                                    :product="product"
                                                    :images="product.images"
                                                />
                                            </div>
                                            <div class="col-4">
                                                <div
                                                    v-if="canProducts.edit"
                                                     ref="dropzone"
                                                     class="p-5 flex justify-center items-center min-h-[200px] bg-dark cursor-pointer text-center rounded"
                                                >
                                                    Нажмите либо поместите ваши изображения здесь
                                                </div>
                                                <div class="my-3">
                                                    <button class="btn btn-primary w-full" @click="saveOrder" v-if="$refs.draggable?.$data?.imgs && $refs.draggable?.$data?.imgs.length > 1">
                                                        Сохранить порядок
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-header text-center">
                                <div class="p-2 text-xl">Категории</div>
                            </div>
                            <div class="card-body">
                                <ProductCategories
                                    :categories-data="this.$props.categoriesData"
                                    :product-data="product"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <form @submit="handleDeleteVariants" class="card">
                            <div class="card-header">
                                <div class="text-center text-xl">Таблица вариантов</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="relative h-10">
                                            <div class="absolute left-2 flex gap-2">
                                                <div>
                                                    <button
                                                        v-if="canProducts.edit"
                                                        type="button" class="btn btn-primary bg-blue"
                                                        data-toggle="modal" data-target="#createOptionsModal"
                                                    >
                                                        Добавить свойство
                                                    </button>
                                                </div>
                                                <div>
                                                    <button
                                                        v-if="canProducts.edit"
                                                        type="button" class="btn btn-danger bg-red-500"
                                                        data-toggle="modal" data-target="#deleteOptionsModal"
                                                    >
                                                        Удалить свойства
                                                    </button>
                                                </div>
                                                <div>
                                                    <!--                            <form class="form-group" action="/admin/options/{{ $product->id }}/values">-->
                                                    <!--                                @csrf-->
                                                    <button
                                                        v-if="canProducts.edit"
                                                        type="submit" class="btn btn-primary bg-blue">
                                                        Изображения для свойств
                                                    </button>
                                                    <!--                            </form>-->
                                                </div>
                                            </div>

                                            <div class="flex absolute right-2">
                                                <div class="mx-4">
                                                    <!--                            <form class="form-group" action="/admin/variants/{{ $product->id }}" method="POST">-->
                                                    <!--                                @csrf-->
                                                    <!--                                @method('DELETE')-->
                                                    <select style="position: fixed; width: 0; height: 0; top: 0; opacity: 0"
                                                            name="variant_ids[]"
                                                            id="delete-select" multiple>
                                                    </select>
                                                    <button
                                                        v-if="canProducts.edit"
                                                        data-delete-variants-button
                                                        type="submit" class="btn btn-danger bg-red">
                                                        Удалить выбранное
                                                    </button>
                                                    <!--                            </form>-->
                                                </div>

                                                <div>
                                                    <button
                                                        v-if="canProducts.edit"
                                                        type="button" class="btn btn-primary bg-blue"
                                                        data-toggle="modal" data-target="#createVariantModal"
                                                    >
                                                        Добавить вариант
                                                    </button>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                   <div class="col-12">
                                       <table class="variants-table">
                                           <thead>
                                           <tr>
                                               <th style="width: 50px"></th>
                                               <th>Фото</th>
                                               <template v-if="product.option_names && product.option_names.length">
                                                   <th v-for="optionName in product.option_names">
                                                       <button
                                                           @click="setSelectedOptionName(optionName)"
                                                           type="button"
                                                           data-toggle="modal" data-target="#editOptionModal"
                                                       >
                                                           <span class="mr-1">{{ optionName.title }}</span>
                                                           <i class="fas fa-palette" v-if="optionName.is_color"></i>

                                                       </button>
                                                   </th>
                                               </template>
                                               <th>Артикул</th>
                                               <th>Цена продажи</th>
                                               <th>Старая цена</th>
                                               <th>Цена закупки</th>
                                               <th>Остаток</th>
                                               <th style="width: 100px;">
                                               </th>
                                           </tr>
                                           </thead>
                                           <tbody v-if="product.variants && product.variants.length">

                                           <tr v-for="variant in product.variants">
                                               <td>
                                                   <div class="flex justify-center">
                                                       <input type="checkbox" class="checkbox" :data-variant-id="variant.id">
                                                   </div>
                                               </td>
                                               <td>
                                                   <div class="flex justify-center">
                                                       <div class="image-button">
                                                           <button
                                                               @click="setSelectedVariant(variant)"
                                                               v-if="variant.images && variant.images.length"
                                                               data-toggle="modal" data-target="#bindImagesToVariant"
                                                               type="button"
                                                           >
                                                               <img :src="variant.images[0].image_url" alt="" width="100" height="100">
                                                           </button>
                                                           <button v-else
                                                                   @click="setSelectedVariant(variant)"
                                                                   data-toggle="modal" data-target="#bindImagesToVariant"
                                                                   type="button"
                                                           >
                                                               <img src="/storage/images/no-image.jpg" alt="" width="100" height="100">
                                                           </button>
                                                       </div>
                                                   </div>
                                               </td>

                                               <template v-for="name in product.option_names">
                                                   <template v-for="value in variant.option_values">

                                                       <td v-if="name.id == value.option_name_id">
                                                           <div class="flex justify-center previous-column">
                                                               <button
                                                                   data-toggle="modal" data-target="#changeVariantOptionsModal"
                                                                   type="button"
                                                                   @click="setSelectedVariant(variant)"
                                                               >{{ value.title }}</button>
                                                           </div>
                                                       </td>

                                                   </template>
                                               </template>

                                               <td>
                                                   <div class="flex justify-center previous-column">
                                                       <div data-field="code" @focusout="updateField($event, variant)"
                                                            v-if="variant.code" contenteditable="true">{{ variant.code }}
                                                       </div>
                                                       <div v-else data-field="code" @focusout="updateField($event, variant)"
                                                            contenteditable="true">—
                                                       </div>

                                                   </div>
                                               </td>
                                               <td>
                                                   <div class="flex justify-center previous-column">
                                                       <div data-field="price" @focusout="updateField($event, variant)"
                                                            v-if="variant.price" contenteditable="true">{{ variant.price }}
                                                       </div>
                                                       <div data-field="price" @focusout="updateField($event, variant)" v-else
                                                            contenteditable="true">—
                                                       </div>
                                                   </div>
                                               </td>
                                               <td>
                                                   <div class="flex justify-center previous-column">
                                                       <div data-field="old_price" @focusout="updateField($event, variant)"
                                                            v-if="variant.old_price" contenteditable="true">{{ variant.old_price }}
                                                       </div>
                                                       <div data-field="old_price" @focusout="updateField($event, variant)" v-else
                                                            contenteditable="true">—
                                                       </div>
                                                   </div>
                                               </td>
                                               <td>
                                                   <div class="flex justify-center previous-column">
                                                       <div data-field="purchase_price" @focusout="updateField($event, variant)"
                                                            v-if="variant.purchase_price" contenteditable="true">
                                                           {{ variant.purchase_price }}
                                                       </div>
                                                       <div data-field="purchase_price" @focusout="updateField($event, variant)" v-else
                                                            contenteditable="true">—
                                                       </div>
                                                   </div>
                                               </td>

<!--                                               <template v-if="groups && groups.length">-->
<!--                                                   <td v-for="group in groups">-->
<!--                                                       &lt;!&ndash;                                    <div class="flex justify-center previous-column">&ndash;&gt;-->
<!--                                                       &lt;!&ndash;                                        <div data-field="quantity" @focusout="updateField($event, variant)"&ndash;&gt;-->
<!--                                                       &lt;!&ndash;                                             v-if="variant.quantity" contenteditable="true">{{ variant.quantity }}&ndash;&gt;-->
<!--                                                       &lt;!&ndash;                                        </div>&ndash;&gt;-->
<!--                                                       &lt;!&ndash;                                        <div data-field="quantity" @focusout="updateField($event, variant)" v-else&ndash;&gt;-->
<!--                                                       &lt;!&ndash;                                             contenteditable="true">—&ndash;&gt;-->
<!--                                                       &lt;!&ndash;                                        </div>&ndash;&gt;-->
<!--                                                       &lt;!&ndash;                                    </div>&ndash;&gt;-->

<!--                                                       <div class="flex justify-center previous-column">-->
<!--                                                           <div v-if="variant.price">{{ variant.price + (variant.price * (group.percents) / 100)}}-->
<!--                                                           </div>-->
<!--                                                           <div data-field="quantity" @click="addFocus" @focusout="updateField($event, variant)" v-else-->
<!--                                                                contenteditable="true">—-->
<!--                                                           </div>-->
<!--                                                       </div>-->
<!--                                                   </td>-->
<!--                                               </template>-->

                                               <td>
                                                   <div class="flex justify-center previous-column">
                                                       <div data-field="quantity" @focusout="updateField($event, variant)"
                                                            v-if="variant.quantity" contenteditable="true">{{ variant.quantity }}
                                                       </div>
                                                       <div data-field="quantity" @focusout="updateField($event, variant)" v-else
                                                            contenteditable="true">—
                                                       </div>
                                                   </div>
                                               </td>



                                               <td style="width: 50px;">
                                                   <!--                                    <form :action="'/admin/variants/' + variant.id +  '/edit'" method="GET">-->
                                                   <button type="submit">Ред.</button>
                                                   <!--                                    </form>-->
                                               </td>
                                           </tr>
                                           </tbody>
                                       </table>
                                   </div>
                                </div>

                            </div>
                        </form>
                    </div>

                </div>
            </section>
            <!-- /.content -->
        </div>
    </AuthenticatedLayout>

</template>

<script>
import Dropzone from 'dropzone'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Link, router} from "@inertiajs/vue3";
import ProductCategories from '@/Pages/Product/ProductCategories.vue'
import DraggableProductImages from '@/Pages/Product/DraggableProductImages.vue'
import ModalImages from '@/Pages/Product/ModalImages.vue'
import CustomSwitch from "@/Components/CustomSwitch.vue";

export default {
    name: "Product",
    components: {
        CustomSwitch,
        AuthenticatedLayout,
        Link,
        ProductCategories,
        DraggableProductImages,
        ModalImages
    },

    props: [
        'productData',
        'categoriesData',
        'canProducts',
    ],
    data() {
        return {
            selectedOptionName: null,
            groups: this.groupsData,
            changeVariantFormData: [],
            selectedVariant: null,
            creatingVariantFormData: this.$props.productData?.option_names,
            optionNames: null,
            isDeleteOptionOpen: null,
            isCreateOptionOpen: null,
            product: this.$props.productData,
            images: this.$props.productData.images,
            dropzone: null,
            createOptionFormData: [
                {
                    id: 1, name: null,
                    defaultValue: null
                }
            ],
            variantCreatingOptions: [{
                id: 1,
                name: null,
                value: null
            }],
            optionsForDeleteForm: null
        }
    },
    methods: {
        addFocus(e) {
          e.target.focus()
        },
        async updateField(event, variant) {
            try {
                let fieldName = event.target.dataset.field
                let data = {
                    field: fieldName,
                    value: event.target.innerText,
                }
                let response = await axios.patch(`/admin/products/${this.product.id}/variants/${variant.id}`, data)
                // console.log(11111)
            } catch (e) {
                event.target.innerText = variant[event.target.dataset.field] || '—'
            }
        },
        async onSubmitImageForm(event) {
            try {
                event.preventDefault()
                let imagesIds = []
                let formElements = [...event.target.elements]
                formElements.map(element => {
                    if (element.classList.contains('image-checkbox') && element.checked) {
                        let imageId = Number(element.id.split('-')[2])
                        imagesIds.push(imageId)
                    }
                })
                let data = {
                    images_ids: imagesIds
                }
                let response = await axios.post(`/admin/products/${this.product.id}/variants/${this.selectedVariant.id}/photos/bind`, data)
                console.log(response)
                this.product = response.data.data
            } catch (e) {
                alert(e)
            }
        },
        setSelectedVariant(variant) {
            this.selectedVariant = variant
            this.product.option_names.map(name => name.edit_form_data_is_new = false)
        },
        async deleteOption(optionNameId) {
            try {
                await axios.delete(`/admin/products/${this.product.id}/options/${optionNameId}`)
                this.product.option_names = this.product.option_names.filter(name => name.id != optionNameId)
                if (!this.product.option_names || (this.product.option_names && !this.product.option_names.length)) {
                    this.creatingVariantFormData = null
                    this.variantCreatingOptions = [{id: 1, name: null, value: null}]
                }


            } catch (e) {
                alert(e)
            }
        },
        async createOptionFetch() {
            try {
                let data = this.createOptionFormData.map(item => ({
                    'name': item.name,
                    'default_value': item.defaultValue
                }))
                await axios.post(`/admin/products/${this.product.id}/options`, {options: data})
                location.reload()
            } catch (e) {
                alert(e)
            }
        },
        async createVariant() {
            try {
                let newOptionValues = []
                let optionsForBind = []
                let data
                if (this.product.option_names && this.product.option_names.length) {
                    this.creatingVariantFormData.map(item => {
                        if (item.creating_variant_selected_id != 'new') {
                            optionsForBind.push(item.creating_variant_selected_id)
                        }
                    })

                    this.creatingVariantFormData.map(item => {
                        if (item.is_new) {
                            newOptionValues.push({
                                'value': item.new_value,
                                'name_id': item.id,
                            })
                        }
                    })
                    data = {
                        newValues: newOptionValues,
                        values: optionsForBind
                    }
                    let response = await axios.post(`/admin/products/${this.product.id}/variants`, data)
                    let newVariant = response.data.data
                    this.product.variants.push(newVariant)


                    if (newOptionValues &&  newOptionValues.length) {

                        newOptionValues.map(newValue => {

                            let val = newVariant.option_values.find(val => val.option_name_id == newValue.name_id)

                            let nameToUpdate = this.creatingVariantFormData.find(name => name.id == newValue.name_id)
                            nameToUpdate?.option_values.push({
                                id: val.id,
                                option_name_id: newValue.name_id,
                                is_default: true,
                                product_id: this.product.id,
                                title: newValue.value,
                            })

                            nameToUpdate.creating_variant_selected_id = +val.id
                            nameToUpdate.is_new = false
                        })
                    }
                } else {
                    data = {
                        newOptions: this.variantCreatingOptions,
                    }
                    await axios.post(`/admin/products/${this.product.id}/variants`, data)
                    location.reload()
                }
            } catch (e) {
                alert(e)
            }
        },
        deleteVariantCreatingOption(id) {
            if (this.variantCreatingOptions.length <= 1) {
                return;
            }
            this.variantCreatingOptions = this.variantCreatingOptions.filter(option => option.id != id)
        },
        async handleClickChangeVariantOptionValue(event, optionName) {
            try {
                let optionValueId = event.target.options[event.target.options.selectedIndex].value
                if (optionValueId == 'new') {
                    optionName.edit_form_data_is_new = true
                } else {
                    let data = {
                        option_name_id: optionName.id,
                        option_value_id: Number(optionValueId),
                    }
                    let response = await axios.post(`/admin/products/${this.product.id}/variants/${this.selectedVariant?.id}/options/bind`, data)
                    let updatedOptionValue = response.data.data
                    this.selectedVariant.option_values = this.selectedVariant.option_values.filter(value => value.option_name_id != optionName.id)
                    this.selectedVariant.option_values.push(updatedOptionValue)
                }

            } catch (e) {
                alert(e)
            }

        },
        addVariantCreatingOption() {
            let lastId = this.variantCreatingOptions[this.variantCreatingOptions.length - 1].id + 1
            this.variantCreatingOptions.push({id: lastId, name: null, value: null})
        },
        handleClickCreateVariantOptionValue(event, dataItem) {
            let selectedIndex = event.target.options.selectedIndex
            dataItem.creating_variant_selected_id = event.target.options[event.target.options.selectedIndex].value
            if (selectedIndex == 0) dataItem.is_new = !dataItem.is_new
            if (dataItem.is_new == true && selectedIndex != 0) dataItem.is_new = false
        },
        deleteOptionInCreateForm(event, optionId) {
            event.stopPropagation()
            this.createOptionFormData = this.createOptionFormData.filter(option => option.id != optionId)
        },
        addOptionNameInForm() {
            let lastId = this.createOptionFormData[this.createOptionFormData.length - 1].id
            let newId = lastId + 1
            this.createOptionFormData.push({id: newId})
        },
        async deleteProduct() {
            try {
                await axios.delete(`/admin/products/${this.product.id}`)
                router.visit('/admin/products', {
                    method: 'GET'
                })
            } catch (e) {
                alert(e)
            }
        },
        async deleteImage(imageId) {
            try {
                await axios.delete(`/admin/products/${this.product.id}/images/${imageId}`)

                let itemToDelete = this.product.images.find(image => image.id == imageId)
                let idx = this.product.images.indexOf(itemToDelete)
                this.product.images.splice(idx, 1)
                if (itemToDelete.variant_id) {
                    let variant = this.product.variants?.find(variant => variant.id == itemToDelete.variant_id)
                    variant.images = variant.images.filter(img => img.id != itemToDelete.id)
                }
            } catch (e) {
                alert(e)
            }
        },
        async storeImage(file) {
            try {
                let formData = new FormData()

                formData.append('image', file)
                formData.append('id', this.product.id)
                let res = await axios.post(`/admin/products/${this.product.id}/images`, formData)
                let newImage = res.data.data
                this.product.images.push(newImage)
                console.log(newImage)
            } catch (e) {
                alert(e)
            }
        },
        async handleDeleteVariants(event) {
            try {
                event.preventDefault()
                //Проверка что форму засабмитила именно кнопка удлить выбранное
                if (event.submitter.dataset.deleteVariantsButton == '' && this.product.variants && this.product.variants.length) {
                    let variantsIdsToDelete = []
                    let formElements = [...event.target.elements]
                    formElements.map(el => {
                        if (el.classList.contains('checkbox') && el.checked) variantsIdsToDelete.push(Number(el.dataset.variantId))
                    })
                    if (!variantsIdsToDelete || !variantsIdsToDelete.length) {
                        return alert('Вы не выбрали варианты!')
                    }

                    await axios.delete(`/admin/products/${this.product.id}/variants?images_ids=${variantsIdsToDelete}`)
                    variantsIdsToDelete.map(variantToDelete => {
                        this.product.variants = this.product.variants.filter(variant => variant.id != variantToDelete)
                    })
                }
            } catch (e) {

            }
        },
        async onSubmitEditOptionsFormNewValue(event, optionName) {
            try {
                event.preventDefault()
                let formElements = [...event.target.elements]
                let newValueInputElement = formElements.find(formElement => formElement.classList.contains('form-control'))
                let newValue = newValueInputElement.value
                let data = {
                    'option_name_id': optionName.id,
                    'value':newValue,
                }
                let response = await axios.post(`/admin/products/${this.product.id}/variants/${this.selectedVariant.id}/options/bind-with-new-value`, data)
                let newOptionValue = response.data.data

                this.selectedVariant.option_values = this.selectedVariant.option_values.filter(value => value.option_name_id != optionName.id)
                this.selectedVariant.option_values.push(newOptionValue)

            } catch (e) {
                alert(e)
            }

        },
        async onChangeOptionColor() {
            try {

                let response = await axios.get(`/admin/products/${this.product.id}/options/${this.selectedOptionName?.id}/toggle-is-color`)
                this.product.option_names.map(name => name.is_color = false)
                this.selectedOptionName.is_color = response.data
            } catch (e) {
                alert(e)
            }
        },
        async saveOrder () {
            try {
                let draggableComponent = this.$refs.draggable
                let draggableData = draggableComponent.$data
                let requestData = draggableData.imgs?.map(img => img.id)
                if (requestData && requestData.length) await axios.post(`/admin/products/${this.product.id}/images/orde`, {order: requestData})
            } catch (e) {
                alert(e?.response?.data?.message ?? e.message)
            }
        },
        setSelectedOptionName(optionName) {
            this.selectedOptionName = optionName
        }
    },
    mounted() {
        console.log(this.product)
        console.log(this.categories)
        if (this.canProducts.edit) {
            this.dropzone = new Dropzone(this.$refs.dropzone, {
                url: '/admin/products/image/test',
                autoProcessQueue: false,
                maxFiles: 10,
                disablePreviews: true
            })
            this.dropzone.on("addedfile", (file) => this.storeImage(file));
        }

        this.creatingVariantFormData = this.creatingVariantFormData.map(item => ({
            ...item, is_new: false, new_value: null, creating_variant_selected_id: item.default_option_value_id
        }))
    }
}
</script>

<style scoped lang='scss'>
table {
    table-layout: fixed;
    width: 100%;
    border-collapse: collapse;
    /*border: 1px solid black;*/
}

.variants-table th, .variants-table td {
    padding: 20px;
    border: 1px solid black;
}
</style>
