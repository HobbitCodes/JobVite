<?php


/**
 * @file
 * Contains \Drupal\jobvite\src\Form\JobviteRunForm.
 */

namespace Drupal\jobvite\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Drupal\node\Entity\Node;
use Drupal\search_api_db\Plugin\search_api\backend\Database;
use Drupal\taxonomy\Entity\Term;

/**
 * Defines a form to configure maintenance settings for this site.
 */
class JobviteRunForm extends ConfigFormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'jobvite_admin_settings_form';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
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
            '#markup' => t("<p>This page allows you to pull in your Jobvite feed manually</p>"),
        );

        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Process jobs'),
            '#button_type' => 'primary',
        );

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

        $url = $this->config('jobvite.settings')->get("jobvite_json_feed_url");

        $url = $url . "?api=" . $this->config('jobvite.settings')->get('jobvite_api') . "&sc=" . $this->config('jobvite.settings')->get('jobvite_secret_key') . "&locName=" . $this->config('jobvite.settings')->get("jobvite_job_location");

        // Make a request to the Jobvite API.

        // Get all job posts
        $storage_handler = \Drupal::entityTypeManager()->getStorage("node");
        $entities = $storage_handler->loadByProperties(["type" => "jobvite_listing"]);

        $currentJobs = array();

        foreach ($entities as $jobPost) {
            $currentJobs[$jobPost->field_jobvite_reference->value] = $jobPost;
        }

        try {
            $response = \Drupal::httpClient()->get($url, array('headers' => array('Accept' => 'text/plain')));
            $data = json_decode($response->getBody());

            if (empty($data)) {
                return FALSE;
            } else {

                foreach ($data->requisitions as $job) {

                    // Create node object with attached file.

                    if (!isset($job->category) || $job->category == "") {
                        $job->category = "None set";
                    }

                    $query = \Drupal::entityQuery('taxonomy_term');
                    $query->condition('vid', "jobvite_category");
                    $query->condition('name', $job->category);
                    $tids = $query->execute();

                    if (count($tids) == 0) {
                        $category = Term::create([
                            'name' => $job->category,
                            'vid' => 'jobvite_category',
                        ])->save();

                        $query = \Drupal::entityQuery('taxonomy_term');
                        $query->condition('vid', "jobvite_category");
                        $query->condition('name', $job->category);
                        $tids = $query->execute();

                        foreach ($tids as $tid) {
                            $cat = $tid;
                        }
                    } else {
                        foreach ($tids as $tid) {
                            $cat = $tid;
                        }
                    }

                    if (!isset($job->department) || $job->department == "") {
                        $job->department = "None set";
                    }

                    $query = \Drupal::entityQuery('taxonomy_term');
                    $query->condition('vid', "jobvite_department");
                    $query->condition('name', $job->department);
                    $tids = $query->execute();


                    if (count($tids) == 0) {

                        $department = Term::create([
                            'name' => (isset($job->department) && $job->department != "" ? $job->department : "None set"),
                            'vid' => 'jobvite_department',
                        ])->save();

                        $query = \Drupal::entityQuery('taxonomy_term');
                        $query->condition('vid', "jobvite_department");
                        $query->condition('name', $job->department);
                        $tids = $query->execute();

                        foreach ($tids as $tid) {
                            $dept = $tid;
                        }
                    } else {
                        foreach ($tids as $tid) {
                            $dept = $tid;
                        }
                    }

                    if (array_key_exists($job->eId, $currentJobs)) {

                        $jobListing = Node::load($currentJobs[$job->eId]->nid->value);

                        $jobListing->body->value = $job->description;
                        $jobListing->body->format = 'full_html';

                        $jobListing->set('field_apply_link', $job->applyLink);

                        $jobListing->save();

                        unset($currentJobs[$job->eId]);

                    } else {
                        $jobListing = Node::create(
                            [
                                'type' => 'jobvite_listing',
                                'path' => ['alias' => '/careers/view/' . $this->slugify($job->title) . '/' . $job->eId],
                                'body' => ['value' => $job->description, 'format' => 'full_html'],
                            ]);
                        $jobListing->set('title', $job->title);
                        $jobListing->set('field_jobvite_reference', $job->eId);
                        $jobListing->set('field_jobvite_location', $job->location);
                        $jobListing->set('field_jobvite_category_new', $cat);
                        $jobListing->set('field_jobvite_department', $dept);
                        $jobListing->set('field_apply_link', $job->applyLink);

                        $jobListing->enforceIsNew();
                        $jobListing->save();
                    }
                }

                $storage_handler->delete($currentJobs);

            }
        } catch (RequestException $e) {
            watchdog_exception('jobvite', $e);
        }

        parent::submitForm($form, $form_state);
    }

    /**
     * URL slug generator
     *
     * TODO: this is used elsewhere - make global helper
     *
     * @param $text
     * @return string
     */
    public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

}