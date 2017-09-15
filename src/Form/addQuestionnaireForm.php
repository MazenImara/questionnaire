<?php

namespace Drupal\questionnaire\Form;

/**
 * @file
 * Contains \Drupal\questionnaire\Form\addQuestionnaireForm.
 */

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\questionnaire\Classes\methods;

class addQuestionnaireForm extends FormBase {
	/**
	 * {@inheritdoc}
	 */
	public function getFormId() {
		return 'addQuestionnaireForm';
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
		$form['days'] = [
			'#type'          => 'textfield',
			'#required'      => TRUE,
			'#default_value' => '30',
			'#description'   => 'Every ^ days',
		];
		$form['description'] = [
			'#type'        => 'textarea',
			'#placeholder' => t('Questionnaire Description'),
			'#required'    => FALSE,
			'#resizable'   => TRUE,
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
		methods::addquestionnaire($form_state->getValues());
	}

}
