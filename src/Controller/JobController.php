<?php

namespace App\Controller;

use App\Entity\Job;
use GuzzleHttp\Client;
use Symfony\Component\CssSelector;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Stmt\Break_;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JobController extends AbstractController
{
    /**
     * @Route("/job", name="job")
     */
    public function index(): Response
    {
        return $this->render('job/index.html.twig', [
            'controller_name' => 'JobController',
        ]);
    }
    /**
     * @Route("/job/detail", name="job_detail")
     */
    public function jobDetail(ManagerRegistry $doctrine): Response
    {
        $conn = $doctrine->getConnection();
        $sql = "SELECT *, MATCH (work) AGAINST ('PHP') as score FROM `CV` WHERE MATCH (work) AGAINST (:search_value) order by score desc";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['search_value' => 'PHP']);
        $candidates = $stmt->fetchAllAssociative();

        return $this->render('job/details.html.twig', [
            'candidates' => $candidates,
        ]);
    }

    /**
     * @Route("/job/search/{search_value}", name="job_search")
     */
    public function search(string $search_value, ManagerRegistry $doctrine): Response
    {

        $conn = $doctrine->getConnection();
        $sql = "SELECT *, MATCH (title,location) AGAINST ('PHP') as score FROM `job` WHERE MATCH (title,location) AGAINST (:search_value) order by score desc";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['search_value' => $search_value]);
        $job = $stmt->fetchAllAssociative();

        return $this->json(['search_result' => $job]);
    }

    /**
     * @Route("/job/create", name="create_job")
     */
    public function createJob(ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        $entityManager = $doctrine->getManager();

        $job = new Job();
        $job->setTitle("PHP (Laravel) developer");
        $job->setCompany("Febian.it");
        $job->setLocation("Moldova Chisinau");
        $job->setDescription("We are currently recruiting skilled PHP Developers to join our business partner's team, a fast growing software company, with a passion for PHP and great code.  In this role, you'll have the opportunity to directly impact the evolution of the product by creating, optimizing, and extending features that bring knowledge and value to end-users.");

        $errors = $validator->validate($job);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        $entityManager->persist($job);
        $entityManager->flush();

        return new Response('Saved new job with id ' . $job->getId());
    }

    /**
     * @Route("/job/crawl/{url}", name="crawl_jobs")
     */
    public function crawlJobs(string $url = 'https://www.bestjobs.eu/ro/locuri-de-munca?location=bucuresti&keyword=symfony', ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {

        $client = new Client();
        $response = $client->request('GET', $url);
        $body = (string) $response->getBody(true);

        $crawler = new Crawler($body);

        $jobs = $crawler->filter('.list-card')
            ->each(function (Crawler $node, $i) {

                $job_ent = new Job();
                $job_ent->setTitle($node->filter('h2 > strong')->text());
                $job_ent->setCompany($node->filter('h3 > small')->text());
                $job_ent->setLocation($node->filter('span > a')->text());
                $job_ent->setDescription("");

                return $job_ent;
            });

        $entityManager = $doctrine->getManager();
        foreach ($jobs as $job) {

            $errors = $validator->validate($job);
            if (count($errors) > 0) {
                return false;
            }

            $entityManager->persist($job);
            $entityManager->flush();
        }

        return new Response('</br>Parsed data from ' . $url . "</br><pre>" . json_encode($jobs) . "</pre>");
    }
}
