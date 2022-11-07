<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $repoArticle;
    private $repoCategory;

    public function __construct(ArticleRepository $repoArticle, CategoryRepository $repoCategory)
    {
        $this->repoArticle = $repoArticle;
        $this->repoCategory = $repoCategory;
    }
    /**
     * @Route("/", name="home")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $categories = $this->repoCategory->findAll();
        $articles = array_reverse($this->repoArticle->findAll());
        $articlesPag = $paginator->paginate(
            $articles, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );
        return $this->render('home/index.html.twig', [
            "articles" => $articlesPag,  // on transmet les articles pagines
            "categories" => $categories,
            "titre" => "Liste des articles"
        ]);
    }

    /**
     * @Route("/showArticles/{id}", name="show_articles")
     */
    public function showArticles(?Category $category,PaginatorInterface $paginator, Request $request): Response
    {
        $categories = $this->repoCategory->findAll();
        if ($category) {
            $articles = $category->getArticles()->getValues();
            $articlesPag = $paginator->paginate(
                $articles, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                6 // Nombre de résultats par page
            );
        } else {
            return $this->redirectToRoute("home");
        }
        return $this->render('home/index.html.twig', [
            "articles" => $articlesPag,
            "categories" =>  $categories,
            "titre" => $category->getTitle()
        ]);
    }

    /**
     * @Route("/show/{id}", name="show")
     */
    public function show(Article $article=null): Response
    {
        if (!$article) {
            return $this->redirectToRoute('home');
        }
        return $this->render('home/show.html.twig', [
            "article" => $article
        ]);
    }

   /**
     * @Route("/recherche", name="recherche")
     */
    public function recherche(Request $request, PaginatorInterface $paginator): Response
    {
        $date = \DateTime::createFromFormat("Y-m-d", date($request->request->get('date')));
        //dd($date);

        $session = new Session();
        if (!$session->get('articles')) {
            $session->start();
        }

        if ($request->request->get('title')) {
            $articles = $this->repoArticle->findByTitleLike($request->request->get('title'), $date);
            $session->set('articles', $articles);
        }

        $articlesPag = $paginator->paginate(
            $session->get('articles'), // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );

        $recherche = (string) $request->request->get('title');

        return $this->render('home/index.html.twig', [
            "articles" => $articlesPag,
            "categories" => $this->repoCategory->findAll(),
            "titre" => "Articles de la recherche :  $recherche"
        ]);

    }
/*
        $articles = $this->repoArticle->findByTitleLike($request->request->get('title'), $date);
        $articlesPag = $paginator->paginate(
            $articles, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );
        return $this->render('home/index.html.twig', [
            "articles" => $articlesPag,
            "categories" => $this->repoCategory->findAll(),
            "titre" => "articles de la recherche"
        ]);
*/

}
