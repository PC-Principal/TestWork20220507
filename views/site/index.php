<?php

/** @var yii\web\View $this */

$this->title = 'TestSite';
$time = array_pop($list);
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
                        <a href="<?=$repo['url']?>" target="_blank"><?=$repo['full_name']?></a> <p class="inline-p">- Дата обновления: <?=$repo['updated']?></p>
                    </div>

                    <div class="col-lg-2">
                        <p><a href="<?=$repo['owner_url']?>" target="_blank"><?=$repo['user']?></a></p>
                        <a href="<?=$repo['owner_url']?>" target="_blank"><img class="avatar-user" src="<?=$repo['ava']?>"></a>
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
