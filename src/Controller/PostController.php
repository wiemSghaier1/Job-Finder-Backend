<?php

namespace App\Controller;

use App\Entity\Employeur;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\AbstractFOSRestController;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("api", name="api_")
 */
class PostController extends AbstractFOSRestController
{

    /**
     * @Route("/postsRecomanded", name="getRecomanded" ,methods={"GET"})
     *  @return JsonResponse
     */
    public function getRecomanded(PostRepository $repository)
    {
        $array = [];
        if ($this->getUser()) { 
            $array = $this->getUser()->getFieldsOfInterests();
            $userCV = $this->getUser()->getCv();
            $userSkills = $userCV -> getFormations();
            
            $array += $userSkills;
            $posts = null;
            if (!empty($array)) {
                $posts = $repository->findByTags($array);
                
            }
            
        }
        $result = [];
        foreach ($posts as $post) {

            $result[] = [
                'id' => $post->getId(),
                'description' => $post->getDescription(),
                'title' => $post->getTitle(),
                'tags' => $post->getTags(),
                'price' => $post->getPrice(),
                'category' => $post->getCategory(),
                'jobType' => $post->getJobType(),
                'location' => $post->getLocation(),
                'createdAt' =>  $post->getCreatedAt()->getTimestamp(),
                'employeur' => [
                    'id' => $post->getEmployeur()->getId(),
                    'fullname' => $post->getEmployeur()->getFullName(),
                    'avatar' => $post->getEmployeur()->getAvatar()
                ],
            ];
        }

         return new JsonResponse($result);
    }

    /**
     * @Route("/posts", name="getPosts" ,methods={"GET"})
     *  @return JsonResponse
     */


    public function getPostsAction(Request $request, PostRepository $repository)
    {   

        $tag = null;
        $jobType = null;
        if ($request->query->get('tag')) {
            $tag = explode(",", $request->query->get('tag'));
        }
        if ($request->query->get('jobType'))
            $jobType =  explode(",", $request->query->get('jobType'));

        if (
            $request->query->get('min') || $request->query->get('max')
            || $tag || $request->query->get('location')
            || $request->query->get('category') ||  $jobType || $request->query->get('search')
        ) {
            $posts = $repository->findByParams(
                $request->query->get('min'),
                $request->query->get('max'),
                $tag,
                $request->query->get('location'),
                $request->query->get('category'),
                $request->query->get('search'),
                $jobType
            );
            // dump("filters");
            //dump($request->query->get('min'), $request->query->get('max'), $tag, $request->query->get('location'), $request->query->get('category'), count($jobType) );

        } else {
            // dump("no filter!");

            $posts = $repository->findAll();
        }

        $result = [];
        foreach ($posts as $post) {

            $result[] = [
                'id' => $post->getId(),
                'description' => $post->getDescription(),
                'title' => $post->getTitle(),
                'tags' => $post->getTags(),
                'price' => $post->getPrice(),
                'category' => $post->getCategory(),
                'jobType' => $post->getJobType(),
                'location' => $post->getLocation(),
                'createdAt' =>  $post->getCreatedAt()->getTimestamp(),
                'employeur' => [
                    'id' => $post->getEmployeur()->getId(),
                    'fullname' => $post->getEmployeur()->getFullName(),
                    'avatar' => $post->getEmployeur()->getAvatar()
                ],
            ];
        }

        return new JsonResponse($result);
    }


    /**
     * @Route("/post/{id}", name="getPostById" ,methods={"GET"})
     *  @return JsonResponse
     */
    public function getPostById(int $id, PostRepository $repository)
    {
        $post = null;
        if ($id) {
            $post = $repository->findOneById($id);
            // dump($post);
        }
        $result[] = [
            'id' => $post->getId(),
            'description' => $post->getDescription(),
            'title' => $post->getTitle(),
            'tags' => $post->getTags(),
            'price' => $post->getPrice(),
            'category' => $post->getCategory(),
            'jobType' => $post->getJobType(),
            'location' => $post->getLocation(),
            'createdAt' =>  $post->getCreatedAt()->getTimestamp(),

            'employeur' => [
                'id' => $post->getEmployeur()->getId(),
                'fullname' => $post->getEmployeur()->getFullName(),
                'avatar' => $post->getEmployeur()->getAvatar(),
                'email' => $post->getEmployeur()->getEmail(),
                'isCompany' => $post->getEmployeur()->getIsCompany(),
                'phoneNumber' => $post->getEmployeur()->getPhoneNumber()
            ],
        ];
        return new JsonResponse($result);
    }

    /**
     * @Route("/deletepost", name="DeletePostsAction" ,methods={"DELETE"})
     *  @return JsonResponse
     */

    public function DeletePostsAction()
    {
        //mana3rach bil id wela bil get user courant
    }

    /**
     * @Route("/post", name="newPost", methods={"POST"})
     * @return Response
     * @throws \Exception
     */
    public function postEmployerAction(Request $request, ValidatorInterface $validator) //post by employer
    {
        $entityManager = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);
        $post = new Post();
        $post->setTitle($data['title']);
        $post->setDescription($data['description']);
        $post->setTags($data['tags']);
        $post->setPrice($data['price']);
        $post->setCategory($data['category']);
        $post->setJobType($data['jobType']);
        $post->setLocation($data['location']);
        $post->setEmployeur($this->getUser());
        $violations = $validator->validate($post);
        if (count($violations) > 0) {
            $result = [];
            foreach ($violations as $violation) {
                $result[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            return new JsonResponse($result, Response::HTTP_BAD_REQUEST);
        }
        $entityManager->persist($post);
        $entityManager->flush();

        return new Response(null, Response::HTTP_CREATED);
    }
}
