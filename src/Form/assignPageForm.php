<?php

namespace Drupal\questionnaire\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\questionnaire\Classes\methods;

class assignPageForm extends FormBase {
	/**
	 * {@inheritdoc}
	 */
	public function getFormId() {
		return 'assignPageForm';
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(array $form, FormStateInterface $form_state) {
		$form['title'] = [
			'#type'        => 'textfield',
			'#placeholder' => t('Title'),
			'#required'    => TRUE,
		];
		$form['link'] = [
			'#type'        => 'textfield',
			'#placeholder' => t('Link'),
			'#required'    => TRUE,
		];
		$form['questionnaireId'] = [
			'#type'        => 'textfield',
			'#placeholder' => t('questionnaireId'),
			'#required'    => TRUE,
		];
		$form['actions']['#type']  = 'actions';
		$form['actions']['submit'] = [
			'#type'        => 'submit',
			'#value'       => $this->t('Assign'),
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
		methods::assignPage($form_state->getValues());
	}

}
