<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Loading Guzzle http client
require_once 'vendor/autoload.php';
use GuzzleHttp\Client;

add_shortcode( 'getro-all-jobs','getro_api_all_jobs_shortcode_function'  );
function getro_api_all_jobs_shortcode_function(  ) {
    ob_start();
    $client = new GuzzleHttp\Client();

    $keyword = '';
    $job_functions = '';
    $job_location = '';
    if (isset($_POST['getroFindJobs'])){
        if (isset($_POST['keyword'])) {
            $keyword = $_POST['keyword'];
            $jobs_endpoint = 'https://api.getro.com/v2/networks/1113/jobs?title='.$keyword.'&per_page=100';

        }
        if (!empty($_POST['job_functions'])) {
            $job_functions = $_POST['job_functions'];
            $jobs_endpoint = 'https://api.getro.com/v2/networks/1113/jobs?job_functions='.$job_functions.'&per_page=20';
        }
        if (!empty($_POST['location'])) {
            $job_location = $_POST['location'];
            $jobs_endpoint = 'https://api.getro.com/v2/networks/1113/jobs?job_functions='.$job_functions.'&locations='.$job_location.'&per_page=50';
        }
    }
    else {
        $jobs_endpoint = 'https://api.getro.com/v2/networks/1113/jobs?per_page=100';
    }


    $jobs_response = $client->request('GET', $jobs_endpoint, [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-User-Email' => 'joy@lexaeon.com',
            'X-User-Token' => 's7SHxxjaAxsxCsvNyxDn',
        ]
    ]);
    $jobs_response_body = $jobs_response->getBody();
    $jobs = json_decode($jobs_response_body);
    $jobs_count = count($jobs->items);
    ?>

    <?php
    // Stop location repeating from the loop
    $job_location = array();
    foreach ($jobs->items as $job_item) {
        foreach ($job_item->locations as $temp_location ) {
            if(!in_array($temp_location, $job_location))
            {
                array_push($job_location, $temp_location);
            }
        }
    }
    ?>

    <div class="fl-content-full container">
        <div class="row">
            <div class="fl-content">
                <div class="col-md-12">
                    <div style="height: 30px"></div>
                    <div class="job-search-form">
                        <form action="" class="form-search filter-listing-form-wrapper" method="post">
                            <div class="form-group tax-select-field">
                                <label class="form-label">Search Job</label>
                                <input class="form-control" type="text" name="keyword">
                            </div>
                            <div class="form-group tax-select-field">
                                <label class="form-label"> Job Functions </label>
                                <select class="form-control form-select" name="job_functions">
                                    <option value="">All</option>
                                    <option value="Marketing & Communications">Marketing & Communications</option>
                                    <option value="Software Engineering">Software Engineering</option>
                                    <option value="IT">IT</option>
                                    <option value="Accounting & Finance">Accounting & Finance</option>
                                    <option value="Other Engineering">Other Engineering</option>
                                    <option value="Product">Product</option>
                                    <option value="People & HR">People & HR</option>
                                    <option value="Customer Service">Customer Service</option>
                                    <option value="Design">Design</option>
                                    <option value="Legal">Legal</option>
                                    <option value="Sales & Business Development">Sales & Business Development</option>
                                    <option value="Operations">Operations</option>
                                    <option value="Data Science">Data Science</option>
                                    <option value="Quality Assurance">Quality Assurance</option>
                                </select>
                            </div>
                            <div class="form-group tax-select-field">
                                <label class="form-label"> Location </label>
                                <select class="form-control form-select" name="location">
                                    <option value="">All</option>
                                    <?php foreach ($job_location as $j_location) { ?>
                                        <option value="<?php echo $j_location; ?>"><?php echo $j_location; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <button type="submit" name="getroFindJobs" class="theme-btn btn-style-one">Find Jobs</button>

                        </form>

                    </div>


                    <div style="margin-left: 18px">
                        <p>Showing <?php echo $jobs_count ?> jobs</p>
                    </div>
                    <?php
                    if ($jobs->items) {
                        foreach ($jobs->items as $item) { ?>
                            <div class="job-block-two col-lg-12">
                                <div class="inner-box">
                                    <div class="content">
                                        <span class="company-logo"><img src="<?php echo $item->company->logo_url; ?>" alt=""></span>
                                        <h4 style="font-size: 20px;  margin-top: 0">
                                            <a href="<?php echo $item->url; ?>"><?php echo $item->title; ?></a>
                                        </h4>
                                        <ul class="job-info" style="padding-left: 0;">
                                            <li>
                                                <span class="icon fas fa-briefcase"></span> <?php echo $item->company->name; ?>
                                            </li>
                                            <?php if ($item->locations) { ?>
                                                <li>
                                                    <span class="icon fas fa-map-marker-alt"></span> <?php echo $item->locations[0]; ?>
                                                </li>
                                            <?php } ?>
                                            <li>
                                                <span class="icon fas fa-clock"></span> <?php echo get_job_timeago(strtotime($item->created_at)); ?>
                                            </li>

                                        </ul>
                                        <div class="getro-apply-now"><a style="background:#ddd" href="<?php echo $item->url; ?>" target="_blank" class="btn btn-secondary white">Apply Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php } ?>
                    <?php } else { ?>
                        <h4 style="margin-left: 18px">No job found</h4>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>


    <?php
    return ob_get_clean();
}