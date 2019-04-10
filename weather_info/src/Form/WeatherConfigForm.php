<?php
/**
 * @file
 * Contains \Drupal\simple\Form\ModuleConfigForm.
 */
namespace Drupal\weather_info\Form;

use Drupal\Core\Form\ConfigFormBase;

use Drupal\Core\Form\FormStateInterface;

class WeatherConfigForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'weatherform_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('weatherform.settings');

    $form['opencagedata_key'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('oepn  cage weather Key '),
      '#default_value' => $config->get('weatherform.opencagedata_key'),
      '#required' => TRUE,
    );
    $form['darksky_key'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Dark sky weather Key'),
      '#default_value' => $config->get('weather.darksky_key'),
      '#required' => TRUE,
    );


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('weatherform.settings');
    $config->set('weatherform.opencagedata_key', $form_state->getValue('opencagedata_key'));
    $config->set('weather.darksky_key', $form_state->getValue('darksky_key'));

    $config->save();

    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'weatherform.settings',
    ];
  }
}