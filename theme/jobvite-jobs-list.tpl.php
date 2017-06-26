<?php

/**
 * @file
 * Default theme implementation to display a list of Jobs.
 *
 * Available variables:
 * - $jobs: The list of job to display as an array of arrays containg the
 *   following properties:
 *   - id: (string) The job id.
 *   - title: (string) The job title.
 *   - description: (string) The job description.
 *   - brief_description: (string) Shorted joib description.
 *   - location: (string) Job location using the "City, State, Country" pattern.
 *   - region: (string) Region
 *   - department: (string) Job's department.
 *   - detail_url: URL to the JobVite detail page for the job.
 *   - apply_url: URL to the JobVite Apply page for the job.
 *   - path: The (internal) path for the job details page.
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $raw_jobs: The list of jobs as raw data objects.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $id: Position of the file. Increments each time it's output.
 */
?>
<div class="<?php print $classes ?>" <?php print $attributes?>>
  <table class="table">
    <?php foreach($jobs as $key => $value):?>
      <tr>
        <td><?php print l($value['title'], $value['path'])?></td>
        <td><?php print $value['location']; ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
