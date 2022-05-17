<?php

/** @var yii\web\View $this */

use yii\helpers\Json;

$this->title = 'TestSite';


?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4 bold-style">Список актуальных репозиториев</h1>

    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-6">
                <p class="block-with-date">Последния актуализация данных произошла: <span class="data-block"><?=$time?></span></p>
            </div>
        </div>

            <?php foreach ($list as $repo): ?>
                <div class="row user-row">
                    <div class="col-lg-12">
                        <a href="<?=$repo['data']['url']?>" target="_blank"><?=$repo['data']['full_name']?></a> <p class="inline-p">- Дата обновления: <?=$repo['repo_date_update']?></p>
                    </div>

                </div>
            <?php endforeach; ?>

        <div class="row">
            <div class="col-lg-6">
                <p class="block-with-date">Последния актуализация данных произошла: <span class="data-block"><?=$time?></span></p>
            </div>
        </div>




    </div>
</div>
