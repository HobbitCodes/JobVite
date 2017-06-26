<?php

/**
 * @file
 * Default theme implementation to display a Job.
 *
 * Available variables:
 * - $title: (string) The job title.
 * - $description: (string) The job description.
 * - $brief_description: (string) Shorted joib description.
 * - $location: (string) Job location using the "City, State, Country" pattern.
 * - $region: (string) Region
 * - $department: (string) Job's department.
 * - $detail_url: URL to the JobVite detail page for the job.
 * - $apply_url: URL to the JobVite Apply page for the job.
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
 * - job: The complete job object.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $id: Position of the file. Increments each time it's output.
 */
?>
<div class="<?php print $classes ?>" <?php print $attributes?>>
  <div class="row">

    <div class="row">
      <div class="job-intro">
        <h2 class="job-application-title"><?php print $title ?></h2>
        <h3><?php print $department ?> | <?php print $location ?></h3>
      </div>

      <div class="job-action-buttons">
        <?php print l(t('Apply'), $apply_url) ?>
      </div>
    </div>

    <div class="job-description">
      <?php print $description ?>
    </div>

  </div>

  <div class="job-apply-bottom">
    <?php print l(t('Apply'), $apply_url) ?>
  </div>

  </article> <!-- /job description -->

</div>
