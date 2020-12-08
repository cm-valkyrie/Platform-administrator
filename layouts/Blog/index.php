<?php
defined('_EXEC') or die;

// Sweet Alert
$this->dependencies->add(['css', '{$path.plugins}sweet-alert2/sweetalert2.min.css']);
$this->dependencies->add(['js', '{$path.plugins}sweet-alert2/sweetalert2.min.js']);

// Pages
$this->dependencies->add(['js', '{$path.js}pages/blog/view.js']);
$this->dependencies->add(['js', '{$path.js}pages/blog/delete.js']);
$this->dependencies->add(['js', '{$path.js}pages/blog/create_categories.js']);
?>
<main class="wrapper">
    <div class="container-fluid">
        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <ol class="breadcrumb hide-phone">
                        <li class="breadcrumb-item">
                            <a href="index.php">{$vkye_webpage}</a>
                        </li>
                        <li class="breadcrumb-item active">Blog</li>
                    </ol>

                    <h4 class="page-title">Blog</h4>
                </div>
            </div>
        </div>
        <!-- end page title end breadcrumb -->

        <div class="row">
            <div class="col-12 m-b-20 d-print-none">
                <div class="button-items text-lg-right">
                    <?php if ( in_array('{blog_create}', Session::get_value('session_permissions')) ): ?>
                        <a href="index.php?c=blog&m=create_article" class="btn btn-success waves-effect waves-light">Nuevo artículo</a>
                    <?php endif; ?>
                    <?php if ( in_array('{categories_blog_read}', Session::get_value('session_permissions')) ): ?>
                        <button class="btn waves-effect waves-light" type="button" data-button-modal="categories">Categorías</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card m-b-30">
                    <div class="card-body">
                        <form name="search" class="m-b-20" data-table-target="blog">
                            <input type="search" name="search" value="" placeholder="Busca por titulo, categoría, fecha de creación o autor.">
                        </form>

                        <div class="table-box-responsive-lg">
                            <table id="blog" class="table m-b-0" style="font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Título</th>
                                        <th>Categoría</th>
                                        <th class="text-center">Fecha de creación</th>
                                        <th>Autor</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ( empty($articles) ) : ?>
                                        <tr>
                                            <td class="table-empty" colspan="5">
                                                No hay ningún artículo en el blog.
                                            </td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php foreach ( $articles as $value ): ?>
                                        <tr>
                                            <td data-title="ID">#<?= $value['id'] ?></td>
                                            <td data-title="Título"><?= $value['title'][Configuration::$lang_default] ?></td>
                                            <td data-title="Categoría"><?= ( is_null($value['category']) ) ? 'Sin categoría' : $value['category'][Configuration::$lang_default] ?></td>
                                            <td data-title="Fecha de creación" class="text-center"><?= Dates::formatted_date($value['publication_date']) ?></td>
                                            <td data-title="Autor"><?= $value['username'] ?></td>

                                            <td data-title="Acciones">
                                                <div class="content-cell">
                                                    <div class="button-items text-right">
                                                        <a href="{$vkye_domain}/blog/<?= $value['url'] ?>" target="_blank" class="btn waves-effect waves-light"><i class="fa fa-link"></i></a>
                                                        <a href="index.php?c=blog&m=update_article&id=<?= $value['id'] ?>" class="btn waves-effect waves-light">Modificar</a>
                                                        <?php if ( in_array('{blog_delete}', Session::get_value('session_permissions')) ): ?>
                                                            <a href="javascript:void(0);" class="btn btn-danger waves-effect waves-light" data-ajax-delete-article="<?= $value['id'] ?>"><i class="fa fa-trash"></i></a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Categorías -->
<?php if ( in_array('{categories_blog_read}', Session::get_value('session_permissions')) ): ?>
    <section id="categories" class="modal" data-modal="categories">
        <div class="content">
            <header>Categorías del blog.</header>
            <main>
                <?php if ( in_array('{categories_blog_create}', Session::get_value('session_permissions')) ): ?>
                    <form name="create_category" class="m-b-20">
                        <div class="row">
                            <div class="col-12 m-b-5">
                                <h6>Agregar categoría.</h6>
                            </div>
                            <div class="col-12 col-md-3 m-b-10">
                                <div class="label">
                                    <label>
                                        <input name="category" type="text"/>
                                        <p class="description">Categoría</p>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 col-md-5 m-b-10">
                                <div class="label">
                                    <label>
                                        <input name="description" type="text"/>
                                        <p class="description">Descripción</p>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 m-b-10">
                                <button type="submit" class="btn btn-block m-t-10">Agregar</button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>

                <table id="users" class="table m-b-0" style="font-size: 14px;">
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <?php if ( in_array('{categories_blog_delete}', Session::get_value('session_permissions')) ): ?>
                                <th></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $categories as $value ): ?>
                            <tr>
                                <td data-title="Categoría"><code><?= $value['category'][Configuration::$lang_default] ?></code></td>
                                <td data-title="Descripción"><?= $value['description'][Configuration::$lang_default] ?></td>

                                <?php if ( in_array('{categories_blog_delete}', Session::get_value('session_permissions')) ): ?>
                                    <td data-title="Acciones">
                                        <div class="content-cell">
                                            <div class="button-items text-right">
                                                <a href="javascript:void(0);" class="btn btn-danger waves-effect waves-light" data-ajax-delete-category="<?= $value['id'] ?>"><i class="fa fa-trash"></i></a>
                                            </div>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </main>
            <footer>
                <div class="action-buttons text-right">
                    <button class="btn btn-link" button-close><small>Cerrar</small></button>
                </div>
            </footer>
        </div>
    </section>
<?php endif; ?>
