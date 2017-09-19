<?php
namespace Drupal\questionnaire\Classes;

class methods {

	static public function getQuetionnaires($id = NULL) {
		$questionnaires = NULL;
		$query          = \Drupal::database()->select('questionnaire', 'q')
		                            ->fields('q', ['id', 'title', 'description', 'days']);
		if ($id) {
			$result = $query->condition('id', [$id])->execute();
			while ($row = $result->fetchAssoc()) {
				$questionnaires = [
					'id'          => $row['id'],
					'title'       => $row['title'],
					'description' => $row['description'],
					'days'        => $row['days'],
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
						'days'        => $row['days'],
					]);
			}
		}
		return $questionnaires;
	}

	static public function addQuestionnaire($questionnaire) {
		\Drupal::database()->insert('questionnaire')
		                   ->fields(['title', 'description', 'days'])
		                   ->values([$questionnaire['title'], $questionnaire['description'], $questionnaire['days']])
		                   ->execute();
	}
	static public function editQuestionnaire($questionnaire) {
		\Drupal::database()->update('questionnaire')
		                   ->condition('id', [$questionnaire['id']])
		                   ->fields(['title' => $questionnaire['title'], 'description' => $questionnaire['description'], 'days' => $questionnaire['days']])
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
		$RQuestion = 0;
		$TQuestion = 0;
		foreach (self::getQuestions($question['questionnaireId']) as $q) {
			if ($q['textAnswer']) {
				$TQuestion++;
			} else {
				$RQuestion++;
			}
		}
		if ($question['textAnswer']) {
			if ($TQuestion < 1) {
				\Drupal::database()->insert('questionnaire_question')
				                   ->fields(['body', 'multichoice', 'textAnswer', 'questionnaireId'])
				                   ->values([$question['body'], $question['multichoice'], $question['textAnswer'], $question['questionnaireId']])
				                   ->execute();
			} else {
				drupal_set_message(t('You can not add more than one text answer question, delete the old and add new one'), 'error');
			}

		} else {
			if ($RQuestion < 4) {
				\Drupal::database()->insert('questionnaire_question')
				                   ->fields(['body', 'multichoice', 'textAnswer', 'questionnaireId'])
				                   ->values([$question['body'], $question['multichoice'], $question['textAnswer'], $question['questionnaireId']])
				                   ->execute();
			} else {
				drupal_set_message(t('You can not add more than four Radio answer question, delete the old and add new one'), 'error');
			}
		}
	}

	static public function getQuestions($questionnaireId) {
		$questions = NULL;
		$result    = \Drupal::database()->select('questionnaire_question', 'q')
		                             ->fields('q', ['id', 'body', 'textAnswer', 'multichoice', 'questionnaireId'])
		                             ->condition('questionnaireId', [$questionnaireId])
		                             ->execute();
		$questions = [];
		while ($row = $result->fetchAssoc()) {
			array_push($questions, [
					'id'              => $row['id'],
					'body'            => $row['body'],
					'multichoice'     => $row['multichoice'],
					'textAnswer'      => $row['textAnswer'],
					'questionnaireId' => $row['questionnaireId'],
				]);
		}
		return $questions;
	}

	static public function getQuestionById($id) {
		$question = NULL;
		$result   = \Drupal::database()->select('questionnaire_question', 'q')
		                             ->fields('q', ['id', 'body', 'multichoice', 'textAnswer', 'questionnaireId'])
		                             ->condition('id', [$id])
		                             ->execute();
		while ($row = $result->fetchAssoc()) {
			$question = [
				'id'              => $row['id'],
				'body'            => $row['body'],
				'multichoice'     => $row['multichoice'],
				'textAnswer'      => $row['textAnswer'],
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
					'textAnswer'      => $q['textAnswer'],
					'questionnaireId' => $q['questionnaireId'],
					'answers'         => self::getAnswers($q['id']),
				]);
		}
		$questionnaire = [
			'id'          => $qnaire['id'],
			'title'       => $qnaire['title'],
			'description' => $qnaire['description'],
			'days'        => $qnaire['days'],
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
	static public function deletePage($id) {
		$link = self::getPageById($id)['link'];
		\Drupal::database()->delete('questionnaire_textanswer', [])
		                   ->condition('link', $link)
		                   ->execute();
		\Drupal::database()->delete('questionnaire_result', [])
		                   ->condition('link', $link)
		                   ->execute();
		\Drupal::database()->delete('questionnaire_page', [])
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

	static public function getPages() {
		$pages  = NULL;
		$result = \Drupal::database()->select('questionnaire_page', 'q')
		                             ->fields('q', ['id', 'title', 'link'])
		                             ->execute();
		$pages = [];
		while ($row = $result->fetchAssoc()) {
			array_push($pages, [
					'id'    => $row['id'],
					'title' => $row['title'],
					'link'  => $row['link'],
				]);
		}
		return $pages;
	}
	static public function getPageById($id) {
		$page   = NULL;
		$result = \Drupal::database()->select('questionnaire_page', 'q')
		                             ->fields('q', ['id', 'title', 'link'])
		                             ->condition('id', $id)
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

	static public function saveResult($post) {
		$answers = [];
		foreach ($post as $a) {
			if (explode(',', $a['name'])[0] != 'textArea') {
				$text = explode(',', $a['value']);
				array_push($answers, [
						'link'     => $text[0],
						'question' => $text[1],
						'answer'   => $text[2],
					]);
				$link = $text[0];
			} else {
				self::saveTextAnswer($a);
				$link = explode(',', $a['name'])[1];
			}
		}

		foreach ($answers as $a) {
			$score = self::getScore($a);
			if (!$score) {
				\Drupal::database()->insert('questionnaire_result')
				                   ->fields(['link', 'question', 'answer', 'score'])
				                   ->values([$a['link'], $a['question'], $a['answer'], '1'])
				                   ->execute();
			} else {
				$score++;
				\Drupal::database()->update('questionnaire_result')
				                   ->condition('link', [$a['link']])
				                   ->condition('question', [$a['question']])
				                   ->condition('answer', [$a['answer']])
				                   ->fields(['score' => $score])
					->execute();
			}
		}
		if (!self::getPageByLink($link)) {
			self::addPage(self::getAssignedPageByLink($link));
		}
	}

	static public function saveTextAnswer($answer) {
		if ($answer['value'] != '') {
			$a = explode(',', $answer['name']);
			\Drupal::database()->insert('questionnaire_textanswer')
			                   ->fields(['link', 'question', 'answer'])
			                   ->values([$a[1], $a[2], $answer['value']])
			                   ->execute();
		}
	}

	static public function getScore($a) {
		$score  = NULL;
		$result = \Drupal::database()->select('questionnaire_result', 'q')
		                             ->fields('q', ['score'])
		                             ->condition('link', $a['link'])
		                             ->condition('question', $a['question'])
		                             ->condition('answer', $a['answer'])
		                             ->execute();
		while ($row = $result->fetchAssoc()) {
			$score = $row['score'];
		}
		return $score;
	}

	static public function getResult($link) {
		$rows   = NULL;
		$result = \Drupal::database()->select('questionnaire_result', 'q')
		                             ->fields('q', ['id', 'link', 'question', 'answer', 'score'])
		                             ->condition('link', $link)
		                             ->execute();
		$rows = [];
		while ($row = $result->fetchAssoc()) {
			array_push($rows, [
					'id'       => $row['id'],
					'link'     => $row['link'],
					'question' => $row['question'],
					'answer'   => $row['answer'],
					'score'    => $row['score'],
				]);
		}
		$questions = [];
		foreach ($rows as $row) {
			if (!in_array($row['question'], $questions)) {
				array_push($questions, $row['question']);
			}
		}
		$questionsArray = [];
		foreach ($questions as $q) {
			$result = \Drupal::database()->select('questionnaire_result', 'q')
			                             ->fields('q', ['answer', 'score'])
			                             ->condition('question', $q)
			                             ->condition('link', $link)
			                             ->execute();
			$answers = [];
			while ($row = $result->fetchAssoc()) {
				array_push($answers, [
						'answer' => $row['answer'],
						'score'  => $row['score'],
					]);
			}

			array_push($questionsArray, ['question' => $q, 'answers' => $answers]);
		}
		return $questionsArray;
	}

	static public function getTextResult($link) {
		$rows   = NULL;
		$result = \Drupal::database()->select('questionnaire_textanswer', 'q')
		                             ->fields('q', ['id', 'link', 'question', 'answer'])
		                             ->condition('link', $link)
		                             ->execute();
		$rows = [];
		while ($row = $result->fetchAssoc()) {
			array_push($rows, [
					'id'       => $row['id'],
					'link'     => $row['link'],
					'question' => $row['question'],
					'answer'   => $row['answer'],
				]);
		}
		$allQuestions = [];
		foreach ($rows as $row) {
			if (!in_array($row['question'], $allQuestions)) {
				array_push($allQuestions, $row['question']);
			}
		}
		$question = [];
		foreach ($allQuestions as $q) {
			$result = \Drupal::database()->select('questionnaire_textanswer', 'q')
			                             ->fields('q', ['answer'])
			                             ->condition('question', $q)
			                             ->condition('link', $link)
			                             ->execute();
			$answers = [];
			while ($row = $result->fetchAssoc()) {
				array_push($answers, $row['answer']);
			}

			array_push($question, ['question' => $q, 'answers' => $answers]);
		}
		return $question;
	}

	static public function deleteResultQuestion($question) {
		\Drupal::database()->delete('questionnaire_result', [])
		                   ->condition('question', $question['question'])
		                   ->condition('link', $question['link'])
		                   ->execute();
	}
	static public function deleteTextResultQuestion($question) {
		\Drupal::database()->delete('questionnaire_textanswer', [])
		                   ->condition('question', $question['question'])
		                   ->condition('link', $question['link'])
		                   ->execute();
	}
}//class