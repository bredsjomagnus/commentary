<?php
/**
 * Routes for the Commentary.
 */
return [
    "routes" => [
        [
            "info" => "Kommentarssida",
            "requestMethod" => null,
            "path" => "commentary",
            "callable" => ["commController", "commentarypage"]
        ],
        [
            "info" => "Lägg till kommentar",
            "requestMethod" => "get|post",
            "path" => "addcomment",
            "callable" => ["commController", "addComment"]
        ],
        [
            "info" => "Redigera kommentar",
            "requestMethod" => "get",
            "path" => "editcomment",
            "callable" => ["commController", "editComment"]
        ],
        [
            "info" => "Redigera kommentar process",
            "requestMethod" => "post",
            "path" => "editcommentprocess",
            "callable" => ["commController", "editCommentProcess"]
        ],
        [
            "info" => "Lägg till gilla process",
            "requestMethod" => "get",
            "path" => "addlikeprocess",
            "callable" => ["commController", "addLikeProcess"]
        ],
        [
            "info" => "Lägg till gilla process",
            "requestMethod" => null,
            "path" => "article/{path:alphanum}",
            "callable" => ["commController", "articleCommentary"]
        ],

        // Articles Routes
        [
            "info" => "Artiklar",
            "requestMethod" => null,
            "path" => "",
            "callable" => ["commController", "getArticles"]
        ],
        [
            "info" => "Create an article",
            "requestMethod" => "get|post",
            "path" => "create",
            "callable" => ["commController", "getPostCreateArticle"],
        ],
        [
            "info" => "Create an article",
            "requestMethod" => "get|post",
            "path" => "delete/{id:digit}",
            "callable" => ["commController", "getPostCreateArticle"],
        ],
        [
            "info" => "Uppdatera artiklar",
            "requestMethod" => "get|post",
            "path" => "update/{id:digit}",
            "callable" => ["commController", "getPostUpdateArticle"],
        ],
    ]
];
