<?php

namespace App\Controller;

use App\Entity\Article;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;

class ArticleController
{
    /**
     * @Get(
     *     path= "/articles/{id}",
     *     name= "app_article_show",
     *     requirements= {"id" = "\d+"}
     * )
     * @View()
     */
    public function showArticle()
    {
        $article = new Article();
        $article
            ->setTitle('Titre de mon premier article')
            ->setContent('Le contenu de on premier article');

        return $article;

    }
}