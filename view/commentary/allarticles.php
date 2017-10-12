<?php
namespace Anax\View;

?>

<?php if (empty($contents)) : ?>
    <div class="container">
        <h3>ARTIKLAR OCH ANNAT INNEHÅLLER PÅ SIDAN</h3>
        <div class="row">
            <div class="col-md-12">
                <a href=<?= $this->di->get("url")->create("create") ?>>Lägg till innehåll</a>
            </div>
        </div>
        <br /><br />
        <p>Det finns inget inlagt innehåll i databasen.</p>
    </div>
<?php
    return;
endif;?>
<div class="container">
    <h3>ARTIKLAR OCH ANNAT INNEHÅLL PÅ SIDAN</h3>
    <div class="row">
        <div class="col-md-12">
            <a href=<?= $this->di->get("url")->create("create") ?>>Lägg till innehåll</a>
        </div>
    </div>
    <br /><br />
    <table class='table'>
        <tr>
            <th>ID</th>
            <th>TITEL</th>
            <th>SLUG</th>
            <th>STATUS</th>
            <th style="max-width: 600px;">INNEHÅLL</th>
        </tr>
    <?php foreach ($contents as $content) : ?>
        <tr>
            <td>
                <?= $content->id ?>
            </td>
            <td>
                <a href=<?= url("update/".$content->id) ?>><?= $content->title ?></a>
            </td>
            <td>
                <?= $content->slug ?>
            </td>
            <td>
                <?= $content->status ?>
            </td>
            <td>
                <?= $content->data ?>
            </td>
        </tr>

    <?php endforeach; ?>
    </table>
</div>
