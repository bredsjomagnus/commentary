<?php
/**
 * Routes for the Commentary.
 */
return [
    "routes" => [
        [
            "info" => "Startsida eller sammanfattningssida med senaste frågorna, mest aktiva användarna mm.",
            "requestMethod" => null,
            "path" => "overview",
            "callable" => ["commController", "overview"]
        ],

        [
            "info" => "Sidan med frågor/artiklar och möjlighet att skapa nya frågor/artiklar",
            "requestMethod" => null,
            "path" => "articles/{tag:alphanum}",
            "callable" => ["commController", "articles"]
        ],
        [
            "info" => "Lägg till svar på en fråga",
            "requestMethod" => "get|post",
            "path" => "addanswerprocess",
            "callable" => ["commController", "addAnswerProcess"]
        ],
        [
            "info" => "Lägg till kommentar på en fråga",
            "requestMethod" => "get|post",
            "path" => "addarticlecommentprocess",
            "callable" => ["commController", "addArticleCommentProcess"]
        ],
        [
            "info" => "Lägg till kommentar på ett svar",
            "requestMethod" => "get|post",
            "path" => "addanswercommentprocess",
            "callable" => ["commController", "addAnswerCommentProcess"]
        ],
        [
            "info" => "Lägg till kommentar på ett svar",
            "requestMethod" => "get|post",
            "path" => "userinfo/{id:digit}",
            "callable" => ["commController", "userInfo"]
        ],

        // vote routes for article
        [
            "info" => "Lägga röst på artikel - process",
            "requestMethod" => "get|post",
            "path" => "votearticleprocess/{id:digit}",
            "callable" => ["commController", "voteArticleProcess"]
        ],
        [
            "info" => "Ångra röst på artikel process",
            "requestMethod" => "get|post",
            "path" => "cancelarticlevote/{id:digit}",
            "callable" => ["commController", "cancelArticleVoteProcess"]
        ],

        // vote routes for articlecomments
        [
            "info" => "Lägg till röst på ett svar - process",
            "requestMethod" => "get|post",
            "path" => "votearticlecommentprocess/{id:digit}",
            "callable" => ["commController", "voteArticleCommentProcess"]
        ],
        [
            "info" => "Lägg till röst på ett svar - process",
            "requestMethod" => "get|post",
            "path" => "cancelarticlecommentvote/{id:digit}",
            "callable" => ["commController", "cancelArticleCommentVoteProcess"]
        ],

        // vote routes for answers
        [
            "info" => "Lägg till röst på ett svar - process",
            "requestMethod" => "get|post",
            "path" => "voteanswerprocess/{id:digit}",
            "callable" => ["commController", "voteAnswerProcess"]
        ],
        [
            "info" => "Ångra röst på svar - process",
            "requestMethod" => "get|post",
            "path" => "cancelanswervote/{id:digit}",
            "callable" => ["commController", "cancelAnswerVoteProcess"]
        ],

        // vote routes for answercomments
        [
            "info" => "Lägg till röst på ett svar - process",
            "requestMethod" => "get|post",
            "path" => "voteanswercommentprocess/{id:digit}",
            "callable" => ["commController", "voteAnswerCommentProcess"]
        ],
        [
            "info" => "Ångra röst på svar - process",
            "requestMethod" => "get|post",
            "path" => "cancelanswercommentvote/{id:digit}",
            "callable" => ["commController", "cancelAnswerCommentVoteProcess"]
        ],

        // Update answer
        [
            "info" => "Ångra röst på svar - process",
            "requestMethod" => "get|post",
            "path" => "updateanswer/{id:digit}",
            "callable" => ["commController", "updateAnswer"]
        ],





        // [
        //     "info" => "Lägg till kommentar",
        //     "requestMethod" => "get|post",
        //     "path" => "createcomment",
        //     "callable" => ["commController", "addComment"]
        // ],
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
            "info" => "Gå till artikel",
            "requestMethod" => null,
            "path" => "article/{id:digit}",
            "callable" => ["commController", "articlePage"]
        ],
        [
            "info" => "Gå till artikel",
            "requestMethod" => null,
            "path" => "tags",
            "callable" => ["commController", "tagsPage"]
        ],

        // Articles Routes
        // [
        //     "info" => "Artiklar",
        //     "requestMethod" => null,
        //     "path" => "",
        //     "callable" => ["commController", "getArticles"]
        // ],
        [
            "info" => "Create an article",
            "requestMethod" => "get|post",
            "path" => "createarticle",
            "callable" => ["commController", "getPostCreateArticle"],
        ],
        [
            "info" => "Create an article",
            "requestMethod" => "get|post",
            "path" => "deletearticle/{id:digit}",
            "callable" => ["commController", "getPostCreateArticle"],
        ],
        [
            "info" => "Uppdatera artiklar",
            "requestMethod" => "get|post",
            "path" => "updatearticle/{id:digit}",
            "callable" => ["commController", "getPostUpdateArticle"],
        ],
    ]
];
