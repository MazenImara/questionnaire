<?php

namespace Drupal\questionnaire\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\questionnaire\Classes\methods;

class addQuestionForm extends FormBase {
	/**
	 * {@inheritdoc}
	 */
	public function getFormId() {
		return 'addQuestion';
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(array $form, FormStateInterface $form_state) {
		$form['body'] = array(
			'#type'        => 'textarea',
			'#placeholder' => t('Question?'),
			'#required'    => TRUE,
			'#resizable'   => TRUE,
		);
		$form['multichoice'] = array(
			'#type'  => 'checkbox',
			'#title' => $this->t('Multichoice'),
		);
		$form['textAnswer'] = array(
			'#type'  => 'checkbox',
			'#title' => $this->t('Text Answer'),
		);
		$form['questionnaireId'] = array(
			'#type'        => 'textfield',
			'#placeholder' => t('questionnaireId'),
			'#required'    => TRUE,
		);
		$form['actions']['#type']  = 'actions';
		$form['actions']['submit'] = array(
			'#type'        => 'submit',
			'#value'       => $this->t('Create'),
			'#button_type' => 'primary',
		);
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
		methods::addQuestion($form_state->getValues());
	}

}
