<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Entity\Score;
use App\Form\ScoreType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ScoreController extends AbstractFOSRestController
{
    /**
     * @Route("/score", name="score")
     * @return JsonResponse

     */
    public function getScoreAction()
    {
        $repository = $this->getDoctrine()->getRepository(Score::class);
        $score = $repository->findall();
        return $this->handleView($this->view($score)); //returns json
    }
    /**
     * @Route("/formation", name="newFormation", methods={"POST"})
     * @return Response
     * @throws \Exception
     */
    public function postScoreAction(Request $request)
    {
        $score = new Score();
        $form = $this->createForm(ScoreType::class, $score);
        $data = json_decode($request->getContent(), true);
        $response = [];
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getConnection()->beginTransaction();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($score);
                $em->flush();
                $this->getDoctrine()->getConnection()->commit();
                return $this->handleView($this->view($score, Response::HTTP_CREATED));
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
