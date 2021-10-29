<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Entity\CV;
use App\Form\CVType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CVController extends AbstractFOSRestController
{
    /**
     * @Route("/cv", name="cv")
     * @return JsonResponse
     */
    public function getCVAction()
    {
        $repository = $this->getDoctrine()->getRepository(CV::class);
        $cv = $repository->findall();
        return $this->handleView($this->view($cv)); //returns json
    }
    /**
     * @Route("/cv ", name="newCV", methods={"POST"})
     * @return Response
     * @throws \Exception
     */
    public function postCVAction(Request $request)
    {
        $cv = new CV();
        $form = $this->createForm(CVType::class, $cv);
        $data = json_decode($request->getContent(), true);
        $response = [];
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getConnection()->beginTransaction();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($cv);
                $em->flush();
                $this->getDoctrine()->getConnection()->commit();
                return $this->handleView($this->view($cv, Response::HTTP_CREATED));
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
