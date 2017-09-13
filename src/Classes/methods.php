<?php
namespace Drupal\questionnaire\Classes;

class methods {

	static public function getQuetionnaires($id = NULL) {
		$questionnaires = NULL;
		$query          = \Drupal::database()->select('questionnaire', 'q')
		                            ->fields('q', ['id', 'title', 'description']);
		if ($id) {
			$result = $query->condition('id', [$id])->execute();
			while ($row = $result->fetchAssoc()) {
				$questionnaires = [
					'id'          => $row['id'],
					'title'       => $row['title'],
					'description' => $row['description'],
				];
			}
		} else {
			$result         = $query->execute();
			$questionnaires = [];
			while ($row = $result->fetchAssoc()) {
				array_push($questionnaires, [
						'id'          => $row['id'],
						'title'       => $row['title'],
						'description' => $row['description'],
					]);
			}
		}
		return $questionnaires;
	}

	static public function addQuestionnaire($questionnaire) {
		\Drupal::database()->insert('questionnaire')
		                   ->fields(['title', 'description'])
		                   ->values([$questionnaire['title'], $questionnaire['description']])
		                   ->execute();
	}

	static public function deleteQuestionnaire($id) {
		$questions = self::getQuestions($id);
		foreach ($questions as $question) {
			self::deleteQuestion($question['id']);
		}
		\Drupal::database()->delete('questionnaire', [])
		                   ->condition('id', [$id])
		                   ->execute();
	}

	static public function addQuestion($question) {
		\Drupal::database()->insert('questionnaire_question')
		                   ->fields(['body', 'multichoice', 'questionnaireId'])
		                   ->values([$question['body'], $question['multichoice'], $question['questionnaireId']])
		                   ->execute();
	}

	static public function getQuestions($questionnaireId) {
		$questions = NULL;
		$result    = \Drupal::database()->select('questionnaire_question', 'q')
		                             ->fields('q', ['id', 'body', 'multichoice', 'questionnaireId'])
		                             ->condition('questionnaireId', [$questionnaireId])
		                             ->execute();
		$questions = [];
		while ($row = $result->fetchAssoc()) {
			array_push($questions, [
					'id'              => $row['id'],
					'body'            => $row['body'],
					'multichoice'     => $row['multichoice'],
					'questionnaireId' => $row['questionnaireId'],
				]);
		}
		return $questions;
	}

	static public function getQuestionById($id) {
		$question = NULL;
		$result   = \Drupal::database()->select('questionnaire_question', 'q')
		                             ->fields('q', ['id', 'body', 'multichoice', 'questionnaireId'])
		                             ->condition('id', [$id])
		                             ->execute();
		while ($row = $result->fetchAssoc()) {
			$question = [
				'id'              => $row['id'],
				'body'            => $row['body'],
				'multichoice'     => $row['multichoice'],
				'questionnaireId' => $row['questionnaireId'],
			];
		}
		return $question;
	}

	static public function deleteQuestion($id) {
		\Drupal::database()->delete('questionnaire_answer', [])
		                   ->condition('questionId', [$id])
		                   ->execute();
		\Drupal::database()->delete('questionnaire_question', [])
		                   ->condition('id', [$id])
		                   ->execute();
	}

	static public function addAnswer($answer) {
		\Drupal::database()->insert('questionnaire_answer')
		                   ->fields(['body', 'questionId'])
		                   ->values([$answer['body'], $answer['questionId']])
		                   ->execute();
	}

	static public function getAnswers($questionId) {
		$answers = NULL;
		$result  = \Drupal::database()->select('questionnaire_answer', 'q')
		                             ->fields('q', ['id', 'body', 'questionId'])
		                             ->condition('questionId', [$questionId])
		                             ->execute();
		$answers = [];
		while ($row = $result->fetchAssoc()) {
			array_push($answers, [
					'id'         => $row['id'],
					'body'       => $row['body'],
					'questionId' => $row['questionId'],
				]);
		}
		return $answers;
	}

	static public function deleteAnswer($id) {
		\Drupal::database()->delete('questionnaire_answer', [])
		                   ->condition('id', [$id])
		                   ->execute();
	}

	static public function getResponseQuestionnaire($id) {
		$questionnaire = NULL;
		$qs            = self::getQuestions($id);
		$qnaire        = self::getQuetionnaires($id);
		$questions     = [];
		foreach ($qs as $q) {
			array_push($questions, [
					'id'              => $q['id'],
					'body'            => $q['body'],
					'multichoice'     => $q['multichoice'],
					'questionnaireId' => $q['questionnaireId'],
					'answers'         => self::getAnswers($q['id']),
				]);
		}
		$questionnaire = [
			'id'          => $qnaire['id'],
			'title'       => $qnaire['title'],
			'description' => $qnaire['description'],
			'questions'   => $questions,
		];
		return $questionnaire;
	}

	static public function assignPage($page) {
		if (!self::getAssignedPageByLink($page['link'])) {
			\Drupal::database()->insert('questionnaire_questionnaire_page')
			                   ->fields(['title', 'link', 'questionnaireId'])
			                   ->values([$page['title'], $page['link'], $page['questionnaireId']])
			                   ->execute();
			self::addPage($page);
		} else {
			drupal_set_message(t('The page is already assigned'), 'error');
		}
	}

	static public function unassignPage($id) {
		\Drupal::database()->delete('questionnaire_questionnaire_page', [])
		                   ->condition('id', [$id])
		                   ->execute();
	}

	static public function getAssignedPages($questionnaireId = NULL) {
		$pages = NULL;
		$query = \Drupal::database()->select('questionnaire_questionnaire_page', 'q')
		                            ->fields('q', ['id', 'title', 'link', 'questionnaireId']);
		if ($questionnaireId) {
			$query->condition('questionnaireId', [$questionnaireId]);
		}
		$result = $query->execute();
		$pages  = [];
		while ($row = $result->fetchAssoc()) {
			array_push($pages, [
					'id'              => $row['id'],
					'title'           => $row['title'],
					'link'            => $row['link'],
					'questionnaireId' => $row['questionnaireId'],
				]);
		}
		return $pages;
	}

	static public function getResponseLinks() {
		$links = NULL;
		$links = self::getAssignedPages();
		return $links;
	}
	static public function getPageByLink($link) {
		$page   = NULL;
		$result = \Drupal::database()->select('questionnaire_page', 'q')
		                             ->fields('q', ['id', 'title', 'link'])
		                             ->condition('link', [$link])
		                             ->execute();
		while ($row = $result->fetchAssoc()) {
			$page = [
				'id'    => $row['id'],
				'title' => $row['title'],
				'link'  => $row['link'],
			];
		}
		return $page;
	}

	static public function getAssignedPageByLink($link) {
		$page   = NULL;
		$result = \Drupal::database()->select('questionnaire_questionnaire_page', 'q')
		                             ->fields('q', ['id', 'title', 'link', 'questionnaireId'])
		                             ->condition('link', [$link])
		                             ->execute();
		while ($row = $result->fetchAssoc()) {
			$page = [
				'id'              => $row['id'],
				'title'           => $row['title'],
				'link'            => $row['link'],
				'questionnaireId' => $row['link'],
			];
		}
		return $page;
	}
	static public function addPage($page) {
		if (!self::getPageByLink($page['link'])) {
			\Drupal::database()->insert('questionnaire_page')
			                   ->fields(['title', 'link'])
			                   ->values([$page['title'], $page['link']])
			                   ->execute();
		}
	}

}//class