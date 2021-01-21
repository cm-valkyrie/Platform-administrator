<div class="col-xl-8">
    <div class="card m-b-30">
        <div class="card-body">
            <!-- Title container -->
            <h4 class="header-title m-t-0">1 - Primer paso.</h4>
            <p class="text-muted m-b-20">Ingresa el titulo de tu artículo, la dirección de enlace se generará automaticamente.</p>
            <!-- End title container -->

            <div class="form-group row">
                <div class="col-md-3">
                    <h6 class="p-t-5">Título del artículo.</h6>
                </div>
                <div class="col-md-9">
                    <div class="label">
                        <label>
                            <input class="form-control" type="text" name="title" data-base-url="span#url" value="<?= ( isset($article) ) ? $article['title'][Configuration::$lang_default] : '' ?>">
                            <p class="description text-muted"><small>{$vkye_domain}/blog/<span id="url"><?= ( isset($article) ) ? $article['url'] : '' ?></span></small></p>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-3">
                    <h6 class="p-t-5">Categoría</h6>
                </div>
                <div class="col-md-9">
                    <div class="label">
                        <label>
                            <select name="category">
                                <option value="">Sin categoría</option>
                                <?php foreach ( $categories as $value ): ?>
                                    <option value="<?= $value['id'] ?>" <?= ( isset($article) && $value['id'] === $article['id_category'] ) ? 'selected' : '' ?>><?= $value['category'][Configuration::$lang_default] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description text-muted">Elige una categoría para el artículo.</p>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card m-b-30">
        <div class="card-body">
            <!-- Title container -->
            <h4 class="header-title m-t-0">2 - Segundo paso.</h4>
            <p class="text-muted m-b-20">Redacta tu artículo.</p>
            <!-- End title container -->

            <div class="label">
                <label>
                    <textarea name="description"><?= ( isset($article) ) ? json_decode($article['article'][Configuration::$lang_default]) : '' ?></textarea>
                </label>
            </div>
        </div>
    </div>
</div>

<div class="col-xl-4">
    <div class="card m-b-30">
        <div class="card-body">
            <!-- Title container -->
            <h4 class="header-title m-t-0">Imágen de portada.</h4>
            <!-- End title container -->

            <div class="label">
                <label>
                    <div class="upload_image_preview">
                        <?php if ( isset($article) ): ?>
                            <figure class="m-0"><img class="img-fluid" src="{$path.root_uploads}<?= $article['image'] ?>"></figure>
                        <?php else: ?>
                            <figure class="m-0"><img class="img-fluid" src="{$path.images}upload-file.svg"></figure>
                        <?php endif; ?>
                        <span class="d-block">Elegir imágen</span>
                        <input type="file" name="image_cover" accept="image/*" />
                    </div>
                </label>
            </div>
        </div>
    </div>

    <div class="card m-b-30">
        <div class="card-body">
            <!-- Title container -->
            <h4 class="header-title m-t-0">Metadata.</h4>
            <p class="text-muted m-b-20">Con esta información podremos ayudar al posicionamiento seo y sem.</p>
            <!-- End title container -->

            <div class="form-group row">
                <div class="col-md-12">
                    <h6 class="p-t-5">Título para redes sociales.</h6>
                </div>
                <div class="col-md-12">
                    <div class="label">
                        <label>
                            <input class="form-control" type="text" name="sm_title" value="<?= ( isset($article) ) ? $article['sm_title'][Configuration::$lang_default] : '' ?>">
                            <p class="description text-muted">Al compartir el artículo, este título se usará en la publicación.</p>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12">
                    <h6 class="p-t-5">Imágen personalizada para redes sociales.</h6>
                </div>
                <div class="col-md-12">
                    <div class="label">
                        <label>
                            <div class="upload_image_preview">
                                <?php if ( isset($article) && !empty($article['sm_image']) ): ?>
                                    <figure class="m-0"><img class="img-fluid" src="{$path.root_uploads}<?= $article['sm_image'] ?>"></figure>
                                <?php else: ?>
                                    <figure class="m-0"><img class="img-fluid" src="{$path.images}upload-file.svg"></figure>
                                <?php endif; ?>
                                <span class="d-block">Elegir imágen</span>
                                <input type="file" name="sm_image_cover" accept="image/*" />
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12">
                    <h6 class="p-t-5">Descripción.</h6>
                </div>
                <div class="col-md-12">
                    <div class="label">
                        <label>
                            <input class="form-control" type="text" name="sm_description" value="<?= ( isset($article) ) ? $article['sm_description'][Configuration::$lang_default] : '' ?>">
                            <p class="description text-muted">Usa una breve descripción para las publicaciónes en redes sociales.</p>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12">
                    <h6 class="p-t-5">Tags.</h6>
                </div>
                <div class="col-md-12">
                    <div class="label">
                        <label>
                            <?php
                                if ( isset($article) && !is_null($article['tags']) )
                                {
                                    $tags = '';
                                    foreach ( $article['tags'] as $value ): $tags .= trim($value) .','; endforeach;
                                    $tags = trim($tags, ',');
                                }
                            ?>
                            <input class="form-control" type="text" name="tags" value="<?= ( isset($article) && isset($tags) ) ? $tags : '' ?>">
                            <p class="description text-muted">Usa palabras separadas por una cóma (,) para establecer los tags. Esto ayudará a los motores de búsqueda a ser más eficientes.</p>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card m-b-30">
        <div class="card-body">
            <button type="submit" class="btn btn-block"><?= ( isset($article) ) ? 'Actualizar' : 'Publicar' ?> artículo</button>
            <a href="index.php?c=blog" class="btn btn-block btn-link p-b-0"><small>Cancelar</small></a>
        </div>
    </div>
</div>
