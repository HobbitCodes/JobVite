<?php


/**
 * @file
 * Contains \Drupal\jobvite\src\Form\JobviteSettingsForm.
 */

namespace Drupal\jobvite\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Site\Settings;
use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines a form to configure maintenance settings for this site.
 */
class JobviteSettingsForm extends ConfigFormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'jobvite_admin_settings_form';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        return [
            'jobvite.settings',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config('jobvite.settings');
        $form['jobvite_api_information'] = array(
            '#markup' => t("<p>This settings page allows you to configure the Jobvite module. You have a part for generic informations for the Jobvite API and an other to filter imported jobs.<br/>Please, report you to the <em>Jobvite Web Service - Version 2.5 – February 6, 2013.pdf</em> API's documentation of Jobvite for more informations.</p>"),
        );

        $form['jobvite_infos'] = array(
            '#type' => 'fieldset',
            '#title' => t('Connection informations'),
            '#collapsible' => FALSE,
            '#collapsed' => FALSE,
        );

        $form['jobvite_infos']['jobvite_api'] = array(
            '#type' => 'textfield',
            '#title' => t('Api key'),
            '#required' => TRUE,
            '#default_value' => $config->get('jobvite_api'),
            '#description' => t('Your API key to access datas of your company'),
        );

        $form['jobvite_infos']['jobvite_secret_key'] = array(
            '#type' => 'textfield',
            '#title' => t('Secret key for Jobvite'),
            '#required' => TRUE,
            '#default_value' => $config->get('jobvite_secret_key'),
            '#description' => t('Your secret key to access datas of your company'),
        );

        $form['jobvite_infos']['jobvite_company_id'] = array(
            '#type' => 'textfield',
            '#title' => t('Company ID'),
            '#required' => TRUE,
            '#default_value' => $config->get('jobvite_company_id'),
            '#description' => t('Your company ID in Jobvite'),
        );

        $form['jobvite_infos']['jobvite_result_count'] = array(
            '#type' => 'textfield',
            '#title' => t('Number of job requested'),
            '#default_value' => $config->get('jobvite_result_count', 100),
            '#required' => TRUE,
            '#element_validate' => array('element_validate_integer_positive'),
            '#description' => t('Number of jobs requested by query to the API.'),
        );

        $form['jobvite_infos']['jobvite_cron_interval'] = array(
            '#type' => 'textfield',
            '#title' => t('Time interval for jobvite'),
            '#default_value' => $config->get('jobvite_cron_interval', 3600),
            '#required' => TRUE,
            '#element_validate' => array('element_validate_integer_positive'),
            '#description' => t('Time in seconds between two importations. 3600 by default to launch the process every hour.'),
        );

        $form['jobvite_infos']['jobvite_json_feed_url'] = array(
            '#type' => 'textfield',
            '#title' => t('Url of the job feed.'),
            '#required' => TRUE,
            '#default_value' => $config->get('jobvite_json_feed_url', "https://api.jobvite.com/v2/jobFeed"),
            '#description' => t('Enter url for the job feed of Jobvite.'),
        );

        $form['jobvite_infos']['jobvite_path_prefix'] = array(
            '#type' => 'textfield',
            '#title' => t('Prefix for the path of job page.'),
            '#description' => t('Presently, the path for individual job page is simply job/%jobvite_job. You could add a prefix of your choice for this path. Example: careers/'),
            '#default_value' => $config->get('jobvite_path_prefix', ""),
        );

        $form['jobvite_filters'] = array(
            '#type' => 'fieldset',
            '#title' => t('Filters on the query'),
            '#collapsible' => TRUE,
            '#collapsed' => TRUE,
        );

        $form['jobvite_filters']['jobvite_job_type'] = array(
            '#type' => 'textfield',
            '#title' => t('Job type'),
            '#default_value' => $config->get('jobvite_job_type', ""),
            '#description' => t('Job type you want recover. Let blank if you want all types.'),
        );

        $form['jobvite_filters']['jobvite_job_category'] = array(
            '#type' => 'textfield',
            '#title' => t('Category of job'),
            '#default_value' => $config->get('jobvite_job_category', ""),
            '#description' => t('Filter your request on a category of job. Let blank to recover jobs of all categories.'),
        );

        $form['jobvite_filters']['jobvite_job_department'] = array(
            '#type' => 'textfield',
            '#title' => t('Department'),
            '#default_value' => $config->get('jobvite_job_department', ""),
            '#description' => t('Filter your request on a specific department for jobs. Let blank to recover jobs of all department.'),
        );

        $form['jobvite_filters']['jobvite_job_location'] = array(
            '#type' => 'textfield',
            '#title' => t('Location for jobs'),
            '#default_value' => $config->get('jobvite_job_location', ""),
            '#description' => t('Location where you want recover jobs. Let blank if you want all locations.'),
        );

        $form['jobvite_filters']['jobvite_job_region'] = array(
            '#type' => 'textfield',
            '#title' => t('Region for jobs'),
            '#default_value' => $config->get('jobvite_job_region', ""),
            '#description' => t('Region where you want jobs are proposed. Let blank if you want all regions.'),
        );

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $values = $form_state->getValues();
        $this->config('jobvite.settings')
            ->set('jobvite_api', $values['jobvite_api'])
            ->set('jobvite_secret_key', $values['jobvite_secret_key'])
            ->set('jobvite_company_id', $values['jobvite_company_id'])
            ->set('jobvite_result_count', $values['jobvite_result_count'])
            ->set('jobvite_cron_interval', $values['jobvite_cron_interval'])
            ->set('jobvite_json_feed_url', $values['jobvite_json_feed_url'])
            ->set('jobvite_path_prefix', $values['jobvite_path_prefix'])
            ->set('jobvite_job_type', $values['jobvite_job_type'])
            ->set('jobvite_job_category', $values['jobvite_job_category'])
            ->set('jobvite_job_department', $values['jobvite_job_department'])
            ->set('jobvite_job_location', $values['jobvite_job_location'])
            ->set('jobvite_job_region', $values['jobvite_job_region'])
            ->save();

        parent::submitForm($form, $form_state);
    }

}