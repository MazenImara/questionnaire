<?php

namespace Drupal\questionnaire\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Drupal\questionnaire\Classes\methods;

class questionnaireController extends ControllerBase {
	/**
	 * Display the markup.
	 *
	 * @return array
	 */
	public function main() {
		return [
			'#attached' => [
				'library'  => [
					'questionnaire/questionnaire_lib',
				],
			],
			'#theme'          => 'main',
			'#content'        => [
				'questionnaires' => methods::getQuetionnaires(),
				'addForm'        => \Drupal::formBuilder()->getForm('Drupal\questionnaire\Form\addQuestionnaireForm'),
				'deleteForm'     => \Drupal::formBuilder()->getForm('Drupal\questionnaire\Form\deleteQuestionnaireForm'),
			],
		];
	}
	public function questionnaire($id) {
		return [
			'#theme'              => 'questionnaire',
			'#content'            => [
				'questionnaires'     => methods::getQuetionnaires($id),
				'questions'          => methods::getQuestions($id),
				'pages'              => methods::getAssignedPages($id),
				'addQuestionForm'    => \Drupal::formBuilder()->getForm('Drupal\questionnaire\Form\addQuestionForm'),
				'deleteQuestionForm' => \Drupal::formBuilder()->getForm('Drupal\questionnaire\Form\deleteQuestionForm'),
				'assignPageForm'     => \Drupal::formBuilder()->getForm('Drupal\questionnaire\Form\assignPageForm'),
				'unassignPageForm'   => \Drupal::formBuilder()->getForm('Drupal\questionnaire\Form\unassignPageForm'),
			],
		];
	}

	public function question($id) {
		return [
			'#theme'      => 'question',
			'#content'    => [
				'question'   => methods::getQuestionById($id),
				'answers'    => methods::getAnswers($id),
				'addForm'    => \Drupal::formBuilder()->getForm('Drupal\questionnaire\Form\addAnswerForm'),
				'deleteForm' => \Drupal::formBuilder()->getForm('Drupal\questionnaire\Form\deleteAnswerForm'),
			]
		];
	}

	public function ajaxQuestionnaire() {
		$questionnaire = [];
		$id            = $_POST['id'];
		$questionnaire = methods::getResponseQuestionnaire($id);
		return new JsonResponse($questionnaire);
	}

	public function ajaxQuestionnaireLinks() {
		$links = [];
		$links = methods::getResponseLinks();
		return new JsonResponse($links);
	}

	public function ajaxAddResult() {
		return new JsonResponse([]);
	}

}// class