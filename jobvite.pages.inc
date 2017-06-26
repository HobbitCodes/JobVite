<?php

/**
 * @file
 * Page callbacks for the Jobvite module.
 */

/**
 * Page callback for a job detail page.
 *
 * @param object $job
 *   The loaded job object (see jobvite_load().).
 *
 * @return array
 *   The content of the page as a renderable array.
 */
function jobvite_job_page($job) {
  $expected_path = jobvite_page_path($job);
  if (current_path() != $expected_path) {
    // Redirect to full path if an incomplete or incorrect one was used.
    drupal_goto($expected_path, array(
      'query' => drupal_get_query_parameters(),
    ), 301);
  }
  return array(
    '#theme' => 'jobvite_job',
    '#job' => $job,
  );
}

/**
 * Page callback for a jobs listing.
 *
 * @return array
 *   The content of the page as a renderable array.
 */
function jobvite_jobs_list_page() {
  $return = '';
  $jobs = jobvite_job_load_multiple(array(), array('title'));

  if ($jobs) {
    $return = array(
      '#theme' => 'jobvite_jobs_list',
      '#jobs' => $jobs,
    );
  }
  return $return;
}

/**
 * Page callback for a redirect URL.
 */
function jobvite_redirect() {
  $p = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);
  if ($index = strpos($p, '/')) {
    list($type, $id) = explode('/', $p, 2);
    if ($type == 'job' && !empty($id)) {
      $prefix_path = variable_get('jobvite_path_prefix', "");
      drupal_goto($prefix_path . "job/{$id}", array(
        'query' => drupal_get_query_parameters($_GET, array('q', 'p')),
      ), 301);
    }
  }
  return MENU_NOT_FOUND;
}
