<?php

namespace Drupal\jobvite\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a listing of Example.
 *
 * File not currently in use
 *
 * TODO: create custom listing page for Jobs
 */
class JobviteListBuilder extends EntityListBuilder {

    /**
     * {@inheritdoc}
     *
     * We override ::render() so that we can add our own content above the table.
     * parent::render() is where EntityListBuilder creates the table using our
     * buildHeader() and buildRow() implementations.
     */
    public function render() {
        $build['description'] = [
            '#markup' => $this->t('List of all current vacancies imported via Jobvite. To amend API settings go to the <a href="@adminlink">Jobvite admin page</a>.', array(
                '@adminlink' => \Drupal::urlGenerator()->generateFromRoute('jobvite.admin_settings'),
            )),
        ];

        $build += parent::render();
        return $build;
    }

    /**
     * {@inheritdoc}
     */
    public function buildHeader() {
        $header['id'] = $this->t('Job ID');
        $header['title'] = $this->t('Job Title');
        return $header + parent::buildHeader();
    }

    /**
     * {@inheritdoc}
     */
    public function buildRow(EntityInterface $entity) {
        $row['id'] = $entity->id();
        $row['title'] = $entity->title;
        return $row + parent::buildRow($entity);
    }

}