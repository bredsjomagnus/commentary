<div class="container">
    <div class="container">
        <h3>SKAPA INNEHÅLL PÅ SIDAN</h3>
        <div class="row">
            <div class="col-md-12">
                <a href=<?= $this->di->get("url")->create("admincontent") ?>>Tillbaka</a>
                <!-- <a href=<?= $this->di->get("url")->create("admincontent/create") ?>>Allt innehåll</a> -->
                <!-- <a href=<?= $this->di->get("url")->create("book/delete") ?>>Ta bort böcker</a> -->
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $form ?>
                <!-- <i class='text-muted'>* Titel och författare måste anges</i> -->
            </div>
        </div>
    </div>
</div>
