<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class DashboardController extends AbstractController
{
    public function index(EntityManagerInterface $entityManager): Response
    {
        $conn = $entityManager->getConnection();

        // Nombre total d'utilisateurs
        $totalUsers = $conn->query('SELECT COUNT(*) AS total_users FROM user')->fetchAssociative();

        // Nombre total de publications
        $totalPosts = $conn->query('SELECT COUNT(*) AS total_posts FROM post')->fetchAssociative();

        // Nombre total de messages
        $totalMessages = $conn->query('SELECT COUNT(*) AS total_messages FROM message')->fetchAssociative();

        // Nombre total de favoris
        $totalFavorites = $conn->query('SELECT COUNT(*) AS total_favorites FROM favorite')->fetchAssociative();

        // Catégories les plus populaires avec le nombre de publications dans chaque catégorie
        $categoryStats = $conn->query('
            SELECT c.name AS category_name, COUNT(p.id) AS total_posts
            FROM category c
            LEFT JOIN post p ON c.id = p.category_id
            GROUP BY c.id
        ')->fetchAllAssociative();

        // Dernières 5 publications
        $recentPosts = $conn->query('
            SELECT title, publication_date
            FROM post
            ORDER BY publication_date DESC
            LIMIT 5
        ')->fetchAllAssociative();

        // Utilisateurs avec le plus de publications
        $topUsersByPosts = $conn->query('
            SELECT u.username, COUNT(p.id) AS total_posts
            FROM user u
            LEFT JOIN post p ON u.id = p.user_id
            GROUP BY u.id
            ORDER BY total_posts DESC
            LIMIT 5
        ')->fetchAllAssociative();

        return $this->render('dashboard/index.html.twig', [
            'totalUsers' => $totalUsers['total_users'],
            'totalPosts' => $totalPosts['total_posts'],
            'totalMessages' => $totalMessages['total_messages'],
            'totalFavorites' => $totalFavorites['total_favorites'],
            'categoryStats' => $categoryStats,
            'recentPosts' => $recentPosts,
            'topUsersByPosts' => $topUsersByPosts,
        ]);
    }
}
