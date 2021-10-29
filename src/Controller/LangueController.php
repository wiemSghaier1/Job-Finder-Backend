<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Entity\Langue;
use App\Form\LangueType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LangueController extends AbstractFOSRestController
{
    /**
     * @Route("/langue", name="langue")
     * @return JsonResponse
     */
    public function getLangueAction()
    {
        $repository = $this->getDoctrine()->getRepository(Langue::class);
        $langue = $repository->findall();
        return $this->handleView($this->view($langue)); //returns json
    }
    /**
     * @Route("/langue ", name="newLangue", methods={"POST"})
     * @return Response
     * @throws \Exception
     */
    public function postExperienceAction(Request $request)
    {
        $langue = new Langue();
        $form = $this->createForm(LangueType::class, $langue);
        $data = json_decode($request->getContent(), true);
        $response = [];
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getConnection()->beginTransaction();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($langue);
                $em->flush();
                $this->getDoctrine()->getConnection()->commit();
                return $this->handleView($this->view($langue, Response::HTTP_CREATED));
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
