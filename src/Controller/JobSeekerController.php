<?php

namespace App\Controller;

use App\Entity\CV;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Entity\JobSeeker;
use App\Form\JobSeekerType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("api", name="api_")
 */
class JobSeekerController extends AbstractFOSRestController
{
  /**
   * @var UserPasswordEncoderInterface
   */
  private $passwordEncoder;


  public function __construct(UserPasswordEncoderInterface $passwordEncoder)
  {
    $this->passwordEncoder = $passwordEncoder;
  }

  /**
   * @Route("/jobseekers", name="getJobSeekers" ,methods={"GET"})
   *  @return JsonResponse
   */

  public function getJobSeekerAction()
  {
    $repository = $this->getDoctrine()->getRepository(JobSeeker::class);
    $JobSeekers = $repository->findall();
    return $this->handleView($this->view($JobSeekers)); //returns json
  }
  /**
   * @Route("/jobseekers", name="newJobSeeker", methods={"POST"})
   * @return Response
   * @throws \Exception
   */
  public function postJobSeekerAction(Request $request)
  {
    //dump($this->getUser());
    $jobSeeker = new JobSeeker();
    $cv = new CV();
    $form = $this->createForm(JobSeekerType::class, $jobSeeker);
    $data = json_decode($request->getContent(), true);
    $response = [];
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $this->getDoctrine()->getConnection()->beginTransaction();
      try {
        $jobSeeker->setCv($cv);
        $jobSeeker->setPassword(
          $this->passwordEncoder->encodePassword($jobSeeker, $jobSeeker->getPassword())
        );
        $em = $this->getDoctrine()->getManager();
        $em->persist($jobSeeker);
        $em->persist($cv);
        $em->flush();
        $this->getDoctrine()->getConnection()->commit();
        return $this->handleView($this->view($jobSeeker, Response::HTTP_CREATED));
      } catch (\Exception $e) {
        // throw $e;
        $this->getDoctrine()->getConnection()->rollback();
        if (str_contains($e->getMessage(), "email")) {
          $response["errors"] = ["email already in use"];
          return $this->handleView($this->view($response, Response::HTTP_BAD_REQUEST));
        } else
          $response["errors"] = ["server error"];

        return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
      }
    } else {
      $errors = [];
      foreach ($form->getErrors(true, true) as $formError) {
        $errors[] = $formError->getMessage();
      }
      $response["errors"] = $errors;
      return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
    }
  }

  /**
   * @Route("/jobseekerInterest/{interest}", name="addJobSeekerInterest" ,methods={"GET"})
   *  @return JsonResponse
   *  @throws \Exception
   */
  public function addJobSeekerInterest(string $interest)
  {
    if ($this->getUser()) {
      $jobSeeker = $this->getUser();
      $tab = $jobSeeker->getFieldsOfInterests();
      if (!in_array($interest, $tab, $strict=false)){
        array_push($tab, $interest);
        $jobSeeker->setFieldsOfInterests($tab);
        $em = $this->getDoctrine()->getManager();
        $em->flush();
      }
      

      return $this->handleView($this->view($jobSeeker, Response::HTTP_OK));
    } else {
      
      $response["errors"] = ["User is not authentified"];
      return $this->handleView($this->view($response, Response::HTTP_UNAUTHORIZED));
    }
  }


  /**
   * @Route("/jobseekers", name="update_jobseeker", methods={"PUT"})
   */
  public function update(Request $request): JsonResponse
  {
    $jobSeeker = $this->getUser();
    $data = json_decode($request->getContent(), true);
    //this works even if its underlined with red
    $this->getDoctrine()->getRepository(JobSeeker::class)->updatejobSeeker($jobSeeker, $data);

    return new JsonResponse(['status' => 'job seeker updated!']);
  }
}
