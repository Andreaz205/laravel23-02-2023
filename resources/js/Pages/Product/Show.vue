<template>
    <Spinner v-if="isLoading"/>
    <AuthenticatedLayout>
        <FlashMessage />
        <!--TODO: Модальное окно для редактирования материала варианта-->
        <MaterialVariantModal :material="selectedMaterial" :material_sets="material_sets" :selected_variant="selectedVariant" @changeVariantMaterial="handleChangeVariantMaterialCallback"/>


        <AppendAccentPropertiesModal
            :all-accent-properties="accentPropertiesData"
            :product-accent-properties="product.accent_properties"
            :product="product"
            @bound="handleAccentPropertiesBound"
            @delete="handleDeleteAccentProperty"
        />


        <CreateAccentPropertyModal @created="handleCreateAccentProperty"/>

        <!--    TODO: Модальное окно для редактирования свойств-->
<!--        <OptionsModal-->
<!--            :product="product"-->
<!--            :product-option-names="this.product.option_names"-->
<!--            :all-option-names="allOptionNamesData"-->
<!--        />-->

        <!--    TODO: Модальное окно для редактирования свойства в целом-->
<!--        <div class="modal fade" id="editOptionModal" tabindex="-1" role="dialog" aria-labelledby="optionNameColorModal"-->
<!--             aria-hidden="true">-->
<!--            <div class="modal-dialog modal-xl" role="document">-->
<!--                <div class="modal-content">-->
<!--                    <div class="modal-header">-->
<!--                        <h5 class="modal-title" id="exampleModalLabel">Редактировать свойство</h5>-->
<!--                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
<!--                            <span aria-hidden="true">&times;</span>-->
<!--                        </button>-->
<!--                    </div>-->
<!--                    <div class="modal-body">-->
<!--                        <div class="form-group">-->
<!--                            <label>Отображать свойтсво как цвет товара</label>-->
<!--                            <CustomSwitch :is-checked="selectedOptionName?.is_color"-->
<!--                                          :switch-id="'option-name-' + selectedOptionName?.id"-->
<!--                                          @changeSwitch="onChangeOptionColor"/>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="modal-footer">-->
<!--                        <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть-->
<!--                        </button>-->
<!--                        &lt;!&ndash;                        <button type="button" class="btn btn-primary bg-blue-500" @click="saveOption">Сохранить&ndash;&gt;-->
<!--                        &lt;!&ndash;                        </button>&ndash;&gt;-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->

<!--        &lt;!&ndash;    TODO: Модальное окно для создания варианта НУЖНО РЕДАКТИРОВАТЬ&ndash;&gt;-->
<!--        <CreateVariantModal-->
<!--            :product-option-names="this.productData?.option_names"-->
<!--            :product="product"-->
<!--            @variantCreated="handleCreatedVariant"-->
<!--        />-->

        <!--    TODO: Модальное окно для редактирования свойств варианта НУЖНО РЕДАКТИРОВАТЬ-->
        <div class="modal fade" id="changeVariantOptionsModal" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalLabel"
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
                                                    :value="getSelectedVariantOptionNameValue(option_name)">
                                                <option value="new">Добавить новое значение</option>
                                                <option v-for="(value, key) in option_name.option_values"
                                                        :key="value.id" :value="value.id">
                                                    {{ value.title }}
                                                </option>
                                            </select>
                                            <form v-if="option_name.edit_form_data_is_new"
                                                  @submit="onSubmitEditOptionsFormNewValue($event, option_name)">
                                                <div class="form-group">
                                                    <label>Введите значение</label>
                                                    <input class="form-control" type="text"
                                                           placeholder="Введите значение">
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-primary bg-blue" type="submit">Сохранить
                                                    </button>
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

        <!--    TODO: Модальное окно для указанаия категории-->
        <div class="modal fade" id="selectCategoryButton" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Выберите категорию</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-hover" style="border: none">
                            <tbody>
                            <tr class="expandable-body">
                                <td style="border: none">
                                    <div class="p-0">
                                        <table class="table table-hover" style="border: none">
                                            <tbody v-if="categories && categories.length">
                                                <template v-for="category in categories">
                                                    <ProductCategoryRow
                                                        v-if="category.parent_category_id == null"
                                                        :category-data="category"
                                                        :product-data="this.$props.productData"
                                                        :select-button="true"
                                                        :is-switch="false"
                                                        @select="handleSelectCategory"
                                                    />
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary bg-gray-500" ref="closeCategoriesModal" data-dismiss="modal">Закрыть
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!--    TODO: Модальное окно для создания варианта-->
        <div class="modal fade" id="createVariantModal" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-center" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Укажитое материалы</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="container-fluid" v-if="materials && materials.length">
                            <div class="row mt-3" v-for="material in materials">

                                <label class="col-4">
                                    Необходимо указать {{material.title}}
                                </label>

                                <div class="col-8">
                                    <ModelSelect
                                        v-if="createVariantForm && createVariantForm.length"
                                        :options="options(material) ?? [{text: 1, value: 1}]"
                                        v-model="createVariantForm.find(createForm => createForm.material_id === material.id).form"
                                    />
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть</button>
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
                        <div
                            class="h-[180px] w-[260px] border-dashed border-2 border-black m-2 flex justify-center items-center cursor-pointer"
                            ref="variant-dropzone"
                        >
                            Добавить фото
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть</button>
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

                                                <div class="row">
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
                                                    <div v-if="product.length">Длина - {{ product.length }}</div>
                                                    <div v-if="product.width">Ширина - {{ product.width }}</div>
                                                    <div v-if="product.height">Высота - {{ product.height }}</div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 text-bold">
                                                    Описание
                                                </div>
                                            </div>

                                            <div class="row my-2">
                                                <div class="col-12">
                                                    {{ product.description }}
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
                                <div class="p-2 text-xl">Категория</div>
                            </div>
                            <div class="card-body">
<!--                                <ProductCategories-->
<!--                                    :categories-data="this.$props.categoriesData"-->
<!--                                    :product-data="product"-->
<!--                                />-->
                                <div v-if="product.category">
                                    {{product.category.name}}
                                    <button
                                        type="button"
                                        class="btn btn-default ml-2"
                                        data-toggle="modal"
                                        data-target="#selectCategoryButton"
                                    >
                                        Изменить
                                    </button>
                                    <button class="btn btn-danger ml-2" @click="clearCategory">
                                        Убрать
                                    </button>
                                </div>
                                <div v-else class="text-center">
                                    <button
                                        type="button"
                                        class="btn btn-primary bg-blue"
                                        data-toggle="modal"
                                        data-target="#selectCategoryButton"
                                    >
                                        Указать категорию
                                    </button>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="flex gap-2">
                                    Товар на витрине:
                                    <CustomSwitch
                                        :is-checked="product.is_published"
                                        @changeSwitch="handlePublishProduct"
                                        switch-id="publish-switch"
                                    />
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
                                            <button class="btn btn-primary w-full"
                                                    @click="saveOrder"
                                            >
                                                Сохранить порядок
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <AccentProperties :accent-properties-props="product.accent_properties" :product="product"/>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header text-center text-lg bg-yellow">
                                Прокручиваемые модели
                            </div>
                            <div class="card-body">
                                <div class="container-fluid ">

                                    <template v-if="models && models.length">
                                        <div v-for="model in models" class="row border rounded-xl mb-3">
                                            <div class="col-12 text-center py-4">
                                                <Link :href="`/admin/products/${product.id}/models/edit`" class="text-lg">
                                                    {{ model.title ?? 'Модель' }}
                                                </Link>
                                            </div>
                                           <div class="col-12 flex justify-center gap-2 items-center" v-if="model.images && model.images.length">
                                                <div v-for="image in model.images" class="w-[200px] h-[200px]">
                                                    <img :src="image.image_url" alt="">
                                                </div>
                                               <div v-if="model.images_count > 5">
                                                   <Link :href="`/admin/products/${product.id}/models/edit`">
                                                       +{{model.images_count - 5}} фото
                                                   </Link>
                                               </div>
                                           </div>
                                            <div class="col-12 text-center my-2">
                                                {{model.images_count}}/32
                                            </div>
                                        </div>
                                    </template>
                                    <div v-else class="text-lg flex justify-center items-center">
                                        У вас пока нет моделей для данного товара
                                    </div>

                                </div>
                            </div>
                            <div class="card-footer">
                                <Link :href="`/admin/products/${product.id}/models/edit`">
                                    <button class="btn btn-primary">
                                        Редактировать
                                    </button>
                                </Link>
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
<!--                                                <div>-->
<!--                                                    <button-->
<!--                                                        v-if="canProducts.edit"-->
<!--                                                        type="button" class="btn btn-primary bg-blue"-->
<!--                                                        data-toggle="modal" data-target="#createOptionsModal"-->
<!--                                                    >-->
<!--                                                        Добавить свойство-->
<!--                                                    </button>-->
<!--                                                </div>-->
<!--                                                <div>-->
<!--                                                    <button-->
<!--                                                        v-if="canProducts.edit"-->
<!--                                                        type="button" class="btn btn-danger bg-red-500"-->
<!--                                                        data-toggle="modal" data-target="#deleteOptionsModal"-->
<!--                                                    >-->
<!--                                                        Удалить свойства-->
<!--                                                    </button>-->
<!--                                                </div>-->
<!--                                                <div>-->
<!--                                                    <button-->
<!--                                                        v-if="canProducts.edit"-->
<!--                                                        type="submit"-->
<!--                                                        class="btn btn-warning bg-warning"-->
<!--                                                    >-->
<!--                                                        <Link href="/admin/options">-->
<!--                                                            Редактировать свойства-->
<!--                                                        </Link>-->
<!--                                                    </button>-->

<!--                                                </div>-->
                                            </div>

                                            <div class="flex absolute right-2">
                                                <div class="mx-4">
                                                    <select
                                                        style="position: fixed; width: 0; height: 0; top: 0; opacity: 0"
                                                        name="variant_ids[]"
                                                        id="delete-select" multiple>
                                                    </select>
                                                    <button
                                                        v-if="canProducts.edit"
                                                        data-delete-variants-button
                                                        type="submit" class="btn btn-danger bg-red"
                                                    >
                                                        Удалить выбранное
                                                    </button>
                                                </div>

                                                <div>
                                                    <button
                                                        v-if="canProducts.edit && this.materials?.length"
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
                                                <th class="border-0"></th>
                                                <th class="border-0"></th>
                                                <th class="border-0"></th>
                                                <template v-if="product.option_names && product.option_names.length">
                                                    <th class="top-header" v-for="optionName in product.option_names"></th>
                                                </template>
                                                <template v-if="materials && materials.length">
                                                    <template v-for="material in materials">
                                                        <th :colspan="material.material_units.length" class="text-center">
                                                            {{material.title}}
                                                        </th>
<!--                                                        <template v-if="material.material_units && material.material_units.length">-->
<!--                                                            <th v-for="unit in material.material_units">-->
<!--                                                            </th>-->
<!--                                                        </template>-->
                                                    </template>
                                                </template>
                                                <th class="border-0"></th>
                                                <th class="border-0"></th>
                                                <th class="border-0"></th>
                                                <th class="border-0"></th>
                                                <th class="border-0"></th>
                                                <template v-if="prices && prices.length">
                                                    <th class="border-0 " v-for="price in prices">
                                                    </th>
                                                </template>
                                                <th class="border-0"></th>
                                            </tr>
                                            <tr>
                                                <th style="width: 50px"></th>

                                                <th>Фото</th>
                                                <th>Цвет</th>
<!--                                                <th>Цвет</th>-->

<!--                                                <template v-if="product.option_names && product.option_names.length">-->
<!--                                                    <th v-for="optionName in product.option_names">-->
<!--                                                        <button-->
<!--                                                            @click="setSelectedOptionName(optionName)"-->
<!--                                                            type="button"-->
<!--                                                            data-toggle="modal" data-target="#editOptionModal"-->
<!--                                                        >-->
<!--                                                            <span class="mr-1">{{ optionName.title }}</span>-->
<!--                                                            <i class="fas fa-palette" v-if="optionName.is_color"></i>-->
<!--                                                        </button>-->
<!--                                                    </th>-->
<!--                                                </template>-->

                                                <template v-if="materials && materials.length">
                                                    <template v-for="material in materials">
                                                        <template v-if="material.material_units && material.material_units.length">
                                                            <th v-for="unit in material.material_units">
                                                                {{unit.title}}
                                                            </th>
                                                        </template>
                                                    </template>
                                                </template>

                                                <th>Артикул</th>
                                                <th>Цена продажи</th>
                                                <th>Старая цена</th>
                                                <th>Цена закупки</th>
                                                <th>Остаток</th>
                                                <template v-if="prices && prices.length">
                                                    <th v-for="price in prices">
                                                        {{ price.title }}
                                                    </th>
                                                </template>
                                                <th style="width: 100px;"></th>
                                            </tr>
                                            </thead>
                                            <tbody v-if="product.variants && product.variants.length">

                                            <tr v-for="variant in product.variants">
                                                <td>
                                                    <div class="flex justify-center">
                                                        <input type="checkbox" class="checkbox"
                                                               :data-variant-id="variant.id">
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
                                                                <img :src="variant.images[0].image_url" alt=""
                                                                     width="100" height="100">
                                                            </button>
                                                            <button v-else
                                                                    @click="setSelectedVariant(variant)"
                                                                    data-toggle="modal"
                                                                    data-target="#bindImagesToVariant"
                                                                    type="button"
                                                            >
                                                                <img src="/storage/images/no-image.jpg" alt=""
                                                                     width="100" height="100">
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="flex justify-center items-center">
                                                        <template v-if="variant?.material_unit_values?.find(value => value.color)">
                                                            <img :src="variant?.material_unit_values?.find(value => value.color).color.image_url" alt="">
                                                        </template>
                                                        <template>
                                                            -
                                                        </template>
                                                    </div>
                                                </td>

                                                <template v-if="materials && materials.length">
                                                    <template v-for="material in materials" :key="material.id">
                                                        <template v-if="material.material_units && material.material_units.length">
                                                            <template v-for="unit in material.material_units" :key="unit.id">

<!--                                                                <template v-if="variant.material_unit_values && variant.material_unit_values.length">-->
<!--                                                                    <template v-for="material_unit_value in variant.material_unit_values" :key="material_unit_value.id">-->
<!--                                                                        <td v-if="material_unit_value.material_unit_id === unit.id">-->
<!--                                                                            <div-->
<!--                                                                                class="flex justify-center items-center"-->
<!--                                                                                @click="handleMaterialUnitValueClick(material)"-->
<!--                                                                                type="button"-->
<!--                                                                                data-toggle="modal"-->
<!--                                                                                data-target="#variantMaterialModal"-->
<!--                                                                            >-->
<!--                                                                                {{material_unit_value.value}}-->
<!--                                                                            </div>-->
<!--                                                                        </td>-->
<!--                                                                    </template>-->
<!--                                                                </template>-->

                                                                <template v-if="variant.material_unit_values && variant.material_unit_values.length && variant.material_unit_values.find(value => value.material_unit_id === unit.id)">
                                                                    <template v-for="material_unit_value in variant.material_unit_values" :key="material_unit_value.id">
                                                                        <td v-if="material_unit_value.material_unit_id === unit.id">
                                                                            <div
                                                                                class="flex justify-center items-center"
                                                                                @click="handleMaterialUnitValueClick(material, variant)"
                                                                                type="button"
                                                                                data-toggle="modal"
                                                                                data-target="#variantMaterialModal"
                                                                            >
                                                                                {{material_unit_value.value}}
                                                                            </div>
                                                                        </td>
                                                                    </template>
                                                                </template>


                                                                <td v-else>
                                                                    <div class="flex justify-center items-center"
                                                                         @click="handleMaterialUnitValueClick(material, variant)"
                                                                         type="button"
                                                                         data-toggle="modal" data-target="#variantMaterialModal"
                                                                    >
                                                                        -
                                                                    </div>
                                                                </td>

                                                            </template>
                                                        </template>
                                                    </template>
                                                </template>

<!--                                                <template v-for="name in product.option_names">-->
<!--                                                    <template v-for="value in variant.option_values">-->

<!--                                                        <td v-if="name.id == value.option_name_id">-->
<!--                                                            <div class="flex justify-center previous-column">-->
<!--                                                                <button-->
<!--                                                                    data-toggle="modal"-->
<!--                                                                    data-target="#changeVariantOptionsModal"-->
<!--                                                                    type="button"-->
<!--                                                                    @click="setSelectedVariant(variant)"-->
<!--                                                                >{{ value.title }}-->
<!--                                                                </button>-->
<!--                                                            </div>-->
<!--                                                        </td>-->

<!--                                                    </template>-->
<!--                                                </template>-->

                                                <td>
                                                    <div class="flex justify-center previous-column">
                                                        <div data-field="code"
                                                             @focusout="updateField($event, variant)"
                                                             @focus="handleFocus"
                                                             v-if="variant.code"
                                                             contenteditable="true"
                                                        >
                                                            {{ variant.code }}
                                                        </div>
                                                        <div v-else
                                                             data-field="code"
                                                             @focusout="updateField($event, variant)"
                                                             @focus="handleFocus"
                                                             contenteditable="true"
                                                        >
                                                            —
                                                        </div>

                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="flex justify-center previous-column">
                                                        <div
                                                            v-if="variant.price" contenteditable="true"
                                                            data-field="price"
                                                            @focusout="updateField($event, variant)"
                                                            @focus="handleFocus"
                                                        >
                                                            {{ variant.price }}

                                                        </div>
                                                        <div
                                                            data-field="price"
                                                            @focus="handleFocus"
                                                            @focusout="updateField($event, variant)" v-else
                                                            contenteditable="true"
                                                        >
                                                            —
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="flex justify-center previous-column">
                                                        <div
                                                            v-if="variant.old_price"
                                                            contenteditable="true"
                                                            data-field="old_price"
                                                            @focusout="updateField($event, variant)"
                                                            @focus="handleFocus"
                                                        >
                                                            {{ variant.old_price }}
                                                        </div>
                                                        <div
                                                            v-else
                                                            data-field="old_price"
                                                            @focusout="updateField($event, variant)"
                                                            @focus="handleFocus"
                                                            contenteditable="true"
                                                        >
                                                            —
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="flex justify-center previous-column">
                                                        <div
                                                            v-if="variant.purchase_price"
                                                            data-field="purchase_price"
                                                            @focusout="updateField($event, variant)"
                                                            contenteditable="true"
                                                            @focus="handleFocus"
                                                        >
                                                            {{ variant.purchase_price }}
                                                        </div>
                                                        <div
                                                            v-else
                                                            data-field="purchase_price"
                                                            @focus="handleFocus"
                                                            @focusout="updateField($event, variant)"
                                                            contenteditable="true">—
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="flex justify-center previous-column">
                                                        <div
                                                            v-if="variant.quantity"
                                                            data-field="quantity"
                                                            @focusout="updateField($event, variant)"
                                                            @focus="handleFocus"
                                                            contenteditable="true"
                                                        >
                                                            {{ variant.quantity }}
                                                        </div>
                                                        <div
                                                            v-else
                                                            data-field="quantity"
                                                            @focusout="updateField($event, variant)"
                                                            @focus="handleFocus"
                                                            contenteditable="true"
                                                        >—
                                                        </div>
                                                    </div>
                                                </td>

                                                <template v-if="prices && prices.length">
                                                    <td v-for="price in prices">

                                                        <template v-for="variant_price in variant.prices">
                                                            <div class="flex justify-center previous-column"
                                                                 v-if="variant_price.price_id == price.id">
                                                                <div
                                                                    v-if="variant_price.price"
                                                                    @focusout="updatePrice($event, variant_price)"
                                                                    contenteditable="true"
                                                                    @focus="handleFocus"
                                                                >
                                                                    {{ variant_price.price }}
                                                                </div>
                                                                <div
                                                                    v-else
                                                                    @focus="handleFocus"
                                                                    @focusout="updatePrice($event, variant_price)"
                                                                    contenteditable="true"
                                                                >
                                                                    —
                                                                </div>
                                                            </div>
                                                        </template>

                                                    </td>
                                                </template>


                                                <td style="width: 50px;">
                                                    <button type="submit">Ред.</button>

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
import CreateVariantModal from "@/Pages/Product/Modal/CreateVariantModal.vue";
import OptionsModal from "@/Pages/Product/Modal/OptionsModal.vue";
import AccentProperties from "@/Components/AccentProperty/AccentProperties.vue";
import CreateAccentPropertyModal from "@/Components/AccentProperty/CreateAccentPropertyModal.vue";
import AppendAccentPropertiesModal from "@/Components/AccentProperty/AppendAccentPropertiesModal.vue";
import ProductCategoryRow from "@/Pages/Product/ProductCategoryRow.vue";
import Spinner from "@/Components/Spinner.vue";
import FlashMessage from "@/Components/FlashMessage.vue";
import MaterialVariantModal from "@/Pages/Product/Modal/MaterialVariantModal.vue";
import {ModelSelect} from "vue-search-select";



export default {
    name: "Product",
    components: {
        MaterialVariantModal,
        FlashMessage,
        Spinner,
        AppendAccentPropertiesModal,
        CreateAccentPropertyModal,
        AccentProperties,
        CreateVariantModal,
        CustomSwitch,
        OptionsModal,
        AuthenticatedLayout,
        Link,
        ProductCategories,
        DraggableProductImages,
        ModalImages,
        ProductCategoryRow,
        ModelSelect
    },

    props: [
        'materials',
        'categories',
        'models',
        'productData',
        'categoriesData',
        'canProducts',
        'prices',
        'allOptionNames',
        'accentPropertiesProps',
        'material_sets'
    ],
            // createVariantForm: this.material_sets,
    data() {
        return {
            createVariantForm: this.materials.map(material => ({
                material_id: material.id,
                form: {text: null, value: null},
            })),
            selectedMaterial: null,
            isLoading: false,
            errors: null,
            accentPropertiesData: this.accentPropertiesProps,
            variantDropzone: null,
            selectedOptionName: null,
            changeVariantFormData: [],
            selectedVariant: null,
            allOptionNamesData: this.allOptionNames,
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
            optionsForDeleteForm: null
        }
    },
    methods: {
        handleChangeVariantMaterialCallback(variant) {
            let searchedVariant = this.product?.variants?.find(v => v.id === variant.id)
            searchedVariant.material_unit_values = variant.material_unit_values
        },
        handleFocus(e) {
            let target = e.target
            let rng, sel;
            if (document.createRange) {
                rng = document.createRange();
                rng.selectNodeContents(target);
                sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(rng);
            } else {
                let rng = document.body.createTextRange();
                rng.moveToElementText(target);
                rng.select();
            }
        },
        handleCreatedVariant(variant) {
            this.product.variants.push(variant)
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
        // async createOptionFetch() {
        //     try {
        //         let data = this.createOptionFormData.map(item => ({
        //             'name': item.name,
        //             'default_value': item.defaultValue
        //         }))
        //         await axios.post(`/admin/products/${this.product.id}/options`, {options: data})
        //         location.reload()
        //     } catch (e) {
        //         alert(e)
        //     }
        // },

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
                    let updatedOptionValue = response.data
                    let searchedValue = this.selectedVariant.option_values.find(value => value.option_name_id == optionName.id)
                    this.selectedVariant.option_values.splice(this.selectedVariant.option_values.indexOf(searchedValue), 1)
                    // this.selectedVariant.option_values = this.selectedVariant.option_values.filter(value => value.option_name_id != optionName.id)
                    // let searchedValue
                    this.selectedVariant.option_values.push(updatedOptionValue)
                }

            } catch (e) {
                alert(e)
            }
        },
        getSelectedVariantOptionNameValue(option_name) {
            return this.selectedVariant.option_values.find(value => value.option_name_id == option_name.id)?.id
        },
        // deleteOptionInCreateForm(event, optionId) {
        //     event.stopPropagation()
        //     this.createOptionFormData = this.createOptionFormData.filter(option => option.id != optionId)
        // },
        // addOptionNameInForm() {
        //     let lastId = this.createOptionFormData[this.createOptionFormData.length - 1].id
        //     let newId = lastId + 1
        //     this.createOptionFormData.push({id: newId})
        // },
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
        async storeVariantImage(file) {
            let formData = new FormData()
            formData.append('image', file)
            let res = await axios.post(`/admin/products/${this.product.id}/variants/${this.selectedVariant.id}/photos`, formData)
            let newImage = res.data.data
            this.product.images.push(newImage)
            let searchedVariant = this.product.variants.find(v => v.id === this.selectedVariant.id)
            searchedVariant?.images?.push(newImage)
            // let newImage = res.data.data
            // this.product.images.push(newImage)
            // console.log(newImage)
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
                    'value': newValue,
                }
                let response = await axios.post(`/admin/products/${this.product.id}/variants/${this.selectedVariant.id}/options/bind-with-new-value`, data)
                let newOptionValue = response.data.data
                let productName = this.product.option_names.find(name => name.id === optionName.id)
                productName.option_values = [...productName.option_values, newOptionValue]
                let searchedValue = this.selectedVariant.option_values.find(value => value.option_name_id == optionName.id)
                this.selectedVariant.option_values.splice(this.selectedVariant.option_values.indexOf(searchedValue), 1)
                this.selectedVariant.option_values = [...this.selectedVariant.option_values, newOptionValue]
                optionName.edit_form_data_is_new = false
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
        async saveOrder() {
            try {
                let draggableComponent = this.$refs.draggable
                let draggableData = draggableComponent.$data
                let requestData = draggableData.imgs?.map(img => img.id)
                if (requestData && requestData.length) await axios.post(`/admin/products/${this.product.id}/images/order`, {order: requestData})
            } catch (e) {
                alert(e?.response?.data?.message ?? e.message)
            }
        },
        handleCreateAccentProperty(property) {
            this.accentPropertiesData.push(property)

        },
        handleAccentPropertiesBound(accentProperties) {
            this.product.accent_properties = accentProperties;
        },
        handleDeleteAccentProperty(property) {
            let searchedProperty = this.accentPropertiesData.find(p => p.id === property.id)

            let index = this.accentPropertiesData.indexOf(searchedProperty)

            this.accentPropertiesData.splice(index, 1)

        },
        handleMaterialUnitValueClick(material, variant) {
            this.selectedVariant = variant
            this.selectedMaterial = material
        },
        async updatePrice(event, price) {
            try {
                let value = +event.target.innerText
                event.target.blur()
                await axios.patch(`/admin/prices/${price.id}`, {price: value})
            } catch (e) {
                event.target.innerText = price.price
                console.log(e)
                // let {errorsList} = handleError(e)
                // alert(errorsList ?? e)
            }
        },
        async handleSelectCategory(category) {
            try {
                this.isLoading = true
                let data = {
                    category_id: category.id
                }
                await axios.patch(`/admin/products/${this.product.id}/categories`, data)
                location.reload()
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                alert(e?.message && e)
            }
        },
        async clearCategory() {
            try {
                this.isLoading = true
                await axios.delete(`/admin/products/${this.product.id}/categories`)
                location.reload()
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                alert(e?.message ?? e)
            }
        },
        async handlePublishProduct() {
            try {
                this.isLoading = true
                await axios.get(`/admin/products/${this.product.id}/toggle-publish`)
                this.product.is_published = !this.product.is_published
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                alert(e?.message ?? e)
            }
        },
        options(material) {
            if (material && this.material_sets && this.material_sets.length) {
                let materialSet = this.material_sets.find(s => s.material_id === material.id)
                let sets = materialSet['sets']
                let options = [];
                sets?.map(set => {
                    let value = set.ids.join('-')
                    options.push({text: set.title, value: value})
                    // let plainTextValues = ''
                    // let valuesIds = ''
                    // console.log(set)
                    // set?.map((setItem, idx) => {
                    //     plainTextValues += setItem.value + ' '
                    //     valuesIds = idx !== set.length - 1 ? valuesIds + setItem.id + '-' : valuesIds + setItem.id
                    // })
                    // options.push({text: plainTextValues, value: valuesIds})
                })
                return options
            }
        }
    },
    mounted() {
        if (this.canProducts.edit) {
            this.dropzone = new Dropzone(this.$refs.dropzone, {
                url: '/admin/products/image/test',
                autoProcessQueue: false,
                maxFiles: 10,
                disablePreviews: true
            })
            this.dropzone.on("addedfile", (file) => this.storeImage(file));
            this.variantDropzone = new Dropzone(this.$refs["variant-dropzone"], {
                url: '/admin/products/image/test',
                autoProcessQueue: false,
                maxFiles: 10,
                disablePreviews: true
            })
            this.variantDropzone.on("addedfile", (file) => this.storeVariantImage(file));
        }
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
