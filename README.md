JobVite integration
===================

This module provides integration with the Jobvite JSON Feed API.

Job Listing
-----------

The jobvite module queries the Jobvite API periodically and stores the job listings in the database.

Three parameters are required for the JSON Feed (ie. `companyId`, `api` and `sc`). The value of these parameters are
stored in configuration variables (`jobvite_company_id`, `jobvite_api` and `jobvite_secret_key`). Additional parameters
can be used to set the start (`start`) and end (`count`) of each request. The number of job requested per query can be
modified using the configuration variable `jobvite_result_count`.

All these parameters are configurable in the settings page available at admin/config/services/jobvite.

Filters in the query
--------------------

It is possible to limit imported jobs with query filters. These filters are configurable in the settings page
of Jobvite module.

Six parameters could be configure to filter jobs in the query (ie. `type`, `availableTo`, `category`, `department`,
`location`, `region`). By default, `availableTo` is 'External'.

The value of these parameter are stored in configuration variables (`jobvite_job_type`, `jobvite_available_to`,
`jobvite_job_category`, `jobvite_job_department`, `jobvite_job_location`, `jobvite_job_region`).

See all jobs
------------

A page listing all jobs is available at `jobs`. The page is built using the `jobvite_load_multiple`
function ordered by location and title. The `jobvite-jobs-list.tpl.php` template is used to display the listing.

Job details
-----------

For each job, a detail page is provide at `/job/%jobvite_job` where `%jobvite_job` is the job ID.

The title of the page is provided by the `jobvite_page_title` function. The content of the page is provide by the
`jobvite_page` function and formatted using the `jobvite-job.tpl.php` template.

Configuration
-------------

The module uses the following configuration variable:

- `jobvite_json_feed_url`: The base URL (without any query string) to the JobVite JSON Feed API.
- `jobvite_company_id`: The JobVite company ID
- `jobvite_api`: The JobVite API key used for the site.
- `jobvite_secret_key`: The JobVite secret key used for the site.
- `jobvite_result_count`: The number of results queried at once.
- `jobvite_cron_interval`: The interval between queries to the Jobvite feed.
- `jobvite_path_prefix`: The path prefix for all job menu items.
- `jobvite_job_type`: Job type for the filter in query.
- `jobvite_available_to`: Publishing settings for the filter in query.
- `jobvite_job_category`: Job category for the filter in query.
- `jobvite_job_department`: Job department for the filter in query.
- `jobvite_job_location`: Job location for the filter in query.
- `jobvite_job_region`: Job region for the filter in query.

Note an administration UI is provided to configure these variables at `admin/config/services/jobvite`.

Implementation details
----------------------

The module provides the following re-usable functions:
- `jobvite_job_load($id)`: Load a job from the database.
- `jobvite_job_load_multiple(array $conditions = array(), array $orderBy = array())`: Load multiple jobs from the
  database filtered by condition(s) and ordered.



