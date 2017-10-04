<?php
$addcommenturl = $app->url->create("addcomment");
$formode = "disabled";
if ($app->session->has('user')) {
    $formode = "";
}
?>
<form action=<?= $addcommenturl ?> method="POST">
    <textarea rows="4" cols="200" name="comment" value="" placeholder="Skriv kommentar här!" <?= $formode ?>></textarea><br />
    <input type="hidden" name="username" value=<?= $app->session->get('user', "") ?>>
    <input type="hidden" name="email" value=<?= $app->session->get('email', "") ?>>
    <input type="submit" name="commentbtn" value="Lägg kommentar" <?= $formode ?>>
    <input type="submit" name="resetdbbtn" value="Rensa databas på kommentarer" <?= $formode ?>>
</form>
