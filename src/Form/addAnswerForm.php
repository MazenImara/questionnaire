<?php

namespace Drupal\questionnaire\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\questionnaire\Classes\methods;

class addAnswerForm extends FormBase {
	/**
	 * {@inheritdoc}
	 */
	public function getFormId() {
		return 'addAnswerForm';
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(array $form, FormStateInterface $form_state) {
		$form['body'] = [
			'#type'        => 'textarea',
			'#placeholder' => t('Answer.'),
			'#required'    => TRUE,
			'#resizable'   => TRUE,
		];
		$form['questionId'] = [
			'#type'        => 'textfield',
			'#placeholder' => t('questionId'),
			'#required'    => TRUE,
		];
		$form['actions']['#type']  = 'actions';
		$form['actions']['submit'] = [
			'#type'        => 'submit',
			'#value'       => $this->t('Create'),
			'#button_type' => 'primary',
		];
		return $form;
	}

	/**
	 * {@inheritdoc}
	 */
	public function validateForm(array&$form, FormStateInterface $form_state) {

	}

	/**
	 * {@inheritdoc}
	 */
	public function submitForm(array&$form, FormStateInterface $form_state) {
		methods::addAnswer($form_state->getValues());
	}

}