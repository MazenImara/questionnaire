<?php

namespace Drupal\questionnaire\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\questionnaire\Classes\methods;

class unassignPageForm extends FormBase {
	/**
	 * {@inheritdoc}
	 */
	public function getFormId() {
		return 'unassignPageForm';
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(array $form, FormStateInterface $form_state) {
		$form['id'] = [
			'#type'        => 'textfield',
			'#placeholder' => t('assignedPageId'),
			'#required'    => TRUE,
		];
		$form['actions']['#type']  = 'actions';
		$form['actions']['submit'] = [
			'#type'        => 'submit',
			'#value'       => $this->t('Unassign'),
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
		methods::unassignPage($form_state->getValues()['id']);
	}

}