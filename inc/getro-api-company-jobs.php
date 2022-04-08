<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Loading Guzzle http client
require_once 'vendor/autoload.php';
use GuzzleHttp\Client;

add_shortcode( 'getro-company-jobs','getro_api_company_jobs_shortcode_function'  );
function getro_api_company_jobs_shortcode_function(  ) {
    ob_start();

    $client = new GuzzleHttp\Client();

    //error_reporting(0);

    $company_name = $_GET['company'];

    ?>
    <?php if (empty($company_name)) { ?>

        <?php
        // Getting company location
        $companies_location_response = $client->request('GET', 'https://api.getro.com/v2/networks/1113/companies', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-User-Email' => 'joy@lexaeon.com',
                'X-User-Token' => 's7SHxxjaAxsxCsvNyxDn',
            ]
        ]);
        $companies_location_response_body = $companies_location_response->getBody();
        $companies_location = json_decode($companies_location_response_body);
        ?>

        <?php
        // Stop location repeating from the loop
        $c_location = array();
        foreach ($companies_location->items as $company_item) {
            foreach ($company_item->locations as $temp_location ) {
                if(!in_array($temp_location, $c_location))
                {
                    array_push($c_location, $temp_location);
                }
            }
        }
        ?>

        <div class="fl-content-full container">
            <div class="row">
                <div class="fl-content col-md-12">
                    <div style="height: 30px"></div>
                    <div class="job-search-form">
                        <form action="" class="form-search filter-listing-form-wrapper" method="post">
                            <div class="form-group tax-select-field">
                                <label class="form-label">Search Companies</label>
                                <input class="form-control" type="text" name="company_keyword">
                            </div>
                            <div class="form-group tax-select-field">
                                <label class="form-label">Location Company</label>
                                <select class="form-control form-select" name="company_location">
                                    <option value="">All</option>
                                    <?php foreach ($c_location as $location) { ?>
                                        <option value="<?php echo $location; ?>"><?php echo $location; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <button type="submit" name="getroFindCompanies" class="theme-btn btn-style-one">Find Jobs</button>

                        </form>

                    </div>

                    <?php
                    if (isset($_POST['getroFindCompanies'])){
                        if (isset($_POST['company_keyword'])) {
                            $company_keyword = $_POST['company_keyword'];
                            $companies_endpoint = 'https://api.getro.com/v2/networks/1113/companies?name='.$company_keyword.'&per_page=100';
                        }
                        if (!empty($_POST['company_location'])) {
                            $company_location = $_POST['company_location'];
                            $companies_endpoint = 'https://api.getro.com/v2/networks/1113/companies?locations='.$company_location.'';
                        }
                    }
                    else {
                        $companies_endpoint = 'https://api.getro.com/v2/networks/1113/companies';
                    }

                    $companies_response = $client->request('GET', $companies_endpoint, [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'X-User-Email' => 'joy@lexaeon.com',
                            'X-User-Token' => 's7SHxxjaAxsxCsvNyxDn',
                        ]
                    ]);
                    $companies_response_body = $companies_response->getBody();
                    $companies = json_decode($companies_response_body);
                    ?>

                    <div style="margin-left: 18px">
                        <p>Showing <?php echo count($companies->items) ?> companies</p>
                    </div>
                    <?php if ($companies->items) { ?>
                        <?php foreach ($companies->items as $item) { ?>
                            <?php
                            $jobs_count_endpoint = 'https://api.getro.com/v2/networks/1113/jobs?companies='.$item->name.'';
                            $jobs_count_response = $client->request('GET', $jobs_count_endpoint, [
                                'headers' => [
                                    'Content-Type' => 'application/json',
                                    'Accept' => 'application/json',
                                    'X-User-Email' => 'joy@lexaeon.com',
                                    'X-User-Token' => 's7SHxxjaAxsxCsvNyxDn',
                                ]
                            ]);
                            $jobs_count_response_body = $jobs_count_response->getBody();
                            $jobs_count = json_decode($jobs_count_response_body);
                            $jobs_count = $jobs_count->meta->total;
                            ?>

                            <a href="<?php echo get_the_permalink() . '?company=' . $item->name . '&id=' . $item->objectID . ''; ?>" class="col-lg-4 col-md-6 col-sm-12">
                                <div class="job-block-four">
                                    <div class="inner-box">
                                        <span class="company-logo"><img src="<?php echo $item->logo_url; ?>" alt=""></span>
                                        <h4><?php echo $item->name; ?></h4>
                                        <?php if ($jobs_count > 0 ) { ?>
                                            <span><?php echo $jobs_count ?> jobs</span>
                                        <?php } ?>
                                        <?php if ($item->locations) { ?>
                                            <div class="location mb-2"><span class="icon fas fa-map-marker-alt"></span> <?php echo $item->locations[0]; ?>
                                            </div>
                                        <?php } ?>
                                        <?php if (!empty($item->description)) { ?>
                                            <div class="location"><?php echo implode(' ', array_slice(explode(' ', $item->description), 0, 20)); ?></div>
                                        <?php } ?>
                                        <div class="location">SEE MORE INFO</div class="location">
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    <?php } ?>
                </div>

            </div>
        </div>
        </div>


    <?php } else { ?>

        <?php
        $comapny_id = $_GET['id'];
        $company_endpoint = 'https://api.getro.com/v2/networks/1113/companies/' . $comapny_id . '';
        $company_info_response = $client->request('GET', $company_endpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-User-Email' => 'joy@lexaeon.com',
                'X-User-Token' => 's7SHxxjaAxsxCsvNyxDn',
            ]
        ]);
        $company_info_response_body = $company_info_response->getBody();
        $compnay_info = json_decode($company_info_response_body);
        ?>

        <?php
        $keyword = '';
        $job_functions = '';
        $job_location = '';
        if (isset($_POST['getroFindJobs'])){
            if (isset($_POST['keyword'])) {
                $keyword = $_POST['keyword'];
                $jobs_endpoint = 'https://api.getro.com/v2/networks/1113/jobs?title='.$keyword.'&companies='.$company_name.'&per_page=100';

            }
            if (!empty($_POST['job_functions'])) {
                $job_functions = $_POST['job_functions'];
                $jobs_endpoint = 'https://api.getro.com/v2/networks/1113/jobs?job_functions='.$job_functions.'&companies='.$company_name.'&per_page=20';
            }
            if (!empty($_POST['location'])) {
                $job_location = $_POST['location'];
                $jobs_endpoint = 'https://api.getro.com/v2/networks/1113/jobs?job_functions='.$job_functions.'&companies='.$company_name.'&locations='.$job_location.'&per_page=20';
            }
        }
        else {
            $jobs_endpoint = 'https://api.getro.com/v2/networks/1113/jobs?companies='.$company_name.'&per_page=100';
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
                    <div class="col-md-4">
                        <div style="height: 75px"></div>
                        <a href="<?php echo get_the_permalink(); ?>">Back to companies</a>
                        <img src="<?php echo $compnay_info->logo_url ?>" alt="" />
                        <h3><?php echo $company_name; ?></h3>
                        <p><?php echo $compnay_info->description; ?> </p><br>
                        <span><?php echo $compnay_info->locations[0]; ?></span>
                        <span><?php echo $compnay_info->domain; ?></span>
                    </div>
                    <div class="col-md-8">
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
    <?php } ?>

    <?php
    return ob_get_clean();
}