<?php

/**
 * @file
 * Contains
 * \Drupal\jobvite\src\Controller\JobViteController
 */

namespace Drupal\jobvite\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Queue;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class JobviteController extends ControllerBase
{


    /**
     * Implements hook_permission().
     */
    function jobvite_permission()
    {
        return array(
            'administer jobvite' => array(
                'title' => t('Administer Jobvite module'),
                'description' => t('Configuration access key and filter for the importation of jobvite.'),
            ),
        );
    }

    /**
     * Returns an administrative overview of all books.
     *
     * @return array
     *   A render array representing the administrative page content.
     */
    public function adminOverview()
    {
        return "here";
    }

    /**
     * Implements hook_menu().
     */
    function jobvite_menu()
    {
        $prefix_path = $this->config('jobvite.settings')->get('jobvite_path_prefix', "");
        $count_arg = substr_count($prefix_path, "/");

        $items[$prefix_path . 'job/%jobvite_job'] = array(
            'access arguments' => array('access content'),
            'title callback' => 'jobvite_job_page_title',
            'title arguments' => array($count_arg + 1),
            'page callback' => 'jobvite_job_page',
            'page arguments' => array($count_arg + 1),
            'file' => 'jobvite.pages.inc',
            'type' => MENU_CALLBACK,
        );

        $items[$prefix_path . '%/job/%jobvite_job'] = array(
            'title' => 'Job detail',
            'title callback' => 'jobvite_job_page_title',
            'title arguments' => array($count_arg + 2),
            'access arguments' => array('access content'),
            'page callback' => 'jobvite_job_page',
            'page arguments' => array($count_arg + 2),
            'file' => 'jobvite.pages.inc',
        );

        $items[$prefix_path . '%/%/job/%jobvite_job'] = array(
            'title' => 'Job detail',
            'title callback' => 'jobvite_job_page_title',
            'title arguments' => array($count_arg + 3),
            'access arguments' => array('access content'),
            'page callback' => 'jobvite_job_page',
            'page arguments' => array($count_arg + 3),
            'file' => 'jobvite.pages.inc',
        );

        $items[$prefix_path . 'jobs'] = array(
            'title' => 'Career',
            'access arguments' => array('access content'),
            'page callback' => 'jobvite_jobs_list_page',
            'file' => 'jobvite.pages.inc',
            'type' => MENU_CALLBACK,
        );

        $items[$prefix_path . 'job'] = array(
            'title' => 'Career',
            'access arguments' => array('access content'),
            'page callback' => 'jobvite_redirect',
            'file' => 'jobvite.pages.inc',
            'type' => MENU_CALLBACK,
        );

        return $items;
    }

    /**
     * Implements hook_theme().
     */
    function jobvite_theme()
    {
        return array(
            'jobvite_job' => array(
                'template' => 'theme/jobvite-job',
                'variables' => array(
                    'job' => NULL,
                ),
                'file' => 'jobvite.theme.inc',
            ),
            'jobvite_jobs_list' => array(
                'template' => 'theme/jobvite-jobs-list',
                'variables' => array(
                    'jobs' => array(),
                ),
                'file' => 'jobvite.theme.inc',
            ),
        );
    }

    /**
     * Returns the path to a job details page.
     *
     * @param object $job
     *   The data object for the job. It must have the following properties:
     *     - id: The ID of the Job
     *     - category: The category of the job
     *     - title: The title of the job.
     *
     * @return string
     *   The path for the job details page.
     */
    function jobvite_page_path($job)
    {
        ctools_include('cleanstring');
        $settings = array(
            'lower case' => TRUE,
            'transliterate' => TRUE,
        );
        $prefix_path = $this->config('jobvite.settings')->get('jobvite_path_prefix', "");
        return $prefix_path . implode('/', array(
            ctools_cleanstring($job->category, $settings),
            ctools_cleanstring($job->title, $settings),
            'job',
            $job->id,
        ));
    }

    /**
     * Custom - for use when creating a custom search form
     *
     * TODO: might not be needed in final release as already built into Drupal Views
     *
     * @return JsonResponse
     */
    public function autocomplete()
    {
        $matches = array();
        $string = $_GET['q'];

        if ($string) {
            $matches = array();
            $query = \Drupal::entityQuery('node')
                ->condition('status', 1)
                ->condition('title', '%' . db_like($string) . '%', 'LIKE')
                ->condition('type', "jobvite_listing")
                ->pager(4);
            //->condition('field_tags.entity.name', 'node_access');
            $nids = $query->execute();
            $result = entity_load_multiple('node', $nids);
            foreach ($result as $row) {

                $node = node_load($row->nid->value);

                $term_object = \Drupal\taxonomy\Entity\Term::load(($node->get("field_jobvite_category_new")->getValue()[0]["target_id"]));

                $cat_name = $term_object->get('name')->value;

                $dept_object = \Drupal\taxonomy\Entity\Term::load(($node->get("field_jobvite_department")->getValue()[0]["target_id"]));

                $dept_name = $dept_object->get('name')->value;

                $matches[] = [
                    'value' => $row->nid->value,
                    'label' => $row->title->value,
                    'body' => $this->cutText($row->body->value, 120, 3) . '...',
                    'dept' => $dept_name,
                    'cat' => $cat_name
                ];
            }
        }

        return new JsonResponse($matches);
    }

    function cutText($text, $max_char, $mode = 2)
    {
        if ($mode == 1) {
            return substr($text, 0, $max_char);
        }

        $char = $text{$max_char - 1};
        switch ($mode) {
            case 2:
                while ($char != ' ') {
                    $char = $text{--$max_char};
                }
            case 3:
                while ($char != ' ') {
                    $char = $text{++$max_char};
                }
        }
        return substr($text, 0, $max_char);
    }

}