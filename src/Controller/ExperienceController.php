<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Entity\Experience;
use App\Form\ExperienceType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("api", name="api_")
 */
class ExperienceController extends AbstractFOSRestController
{
    /**
     * @Route("/experience", name="experience")
     * @return JsonResponse
     */
    public function getExperienceAction()
    {
        $repository = $this->getDoctrine()->getRepository(Experience::class);
        $Experiences = $repository->findall();
        return $this->handleView($this->view($Experiences)); //returns json
    }
    /**
     * @Route("/experience ", name="newExperience", methods={"POST"})
     * @return Response
     * @throws \Exception
     */
    public function postExperienceAction(Request $request)
    {
        $experience = new Experience();
        $form = $this->createForm(ExperienceType::class, $experience);
        $data = json_decode($request->getContent(), true);
        $response = [];
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getConnection()->beginTransaction();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($experience);
                $em->flush();
                $this->getDoctrine()->getConnection()->commit();
                return $this->handleView($this->view($experience, Response::HTTP_CREATED));
            } catch (\Exception $e) {
                // throw $e;
                $this->getDoctrine()->getConnection()->rollback();
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
}
