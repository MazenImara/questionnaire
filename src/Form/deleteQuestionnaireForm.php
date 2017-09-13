<?php

namespace Drupal\questionnaire\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Drupal\questionnaire\Classes\methods;

class deleteQuestionnaireForm extends FormBase {
	/**
	 * {@inheritdoc}
	 */
	public function getFormId() {
		return 'deleteQuestionnaireForm';
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(array $form, FormStateInterface $form_state) {
		$form['id'] = array(
			'#type'        => 'textfield',
			'#placeholder' => t('id'),
			'#required'    => TRUE,
		);
		$form['actions']['#type']  = 'actions';
		$form['actions']['submit'] = array(
			'#type'        => 'submit',
			'#value'       => $this->t('Delete'),
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
		methods::deleteQuestionnaire($form_state->getValues()['id']);
		$response = new RedirectResponse('/questionnaire');
		$response->send();
		drupal_set_message(t('Questionnaire has been deleted successfuly'));
	}

}