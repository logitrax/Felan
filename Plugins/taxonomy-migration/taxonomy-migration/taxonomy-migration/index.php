<?php

/**
 * Plugin Name: Uxper Taxonomy Migration
 * Description: Migrate data from taxonomy.
 * Version: 1.0
 * Author: Uxper
 */

if (!defined('ABSPATH')) {
    exit; // Protect the plugin
}

function migrate_taxonomy_data()
{
    global $wpdb;

    $state_taxonomies = ['jobs-state', 'company-state', 'service-state', 'freelancer-state', 'project-state'];
    $location_taxonomies = ['jobs-location', 'company-location', 'service-location', 'freelancer_locations', 'project-location'];
    $state_mapping = [];
    $location_mapping = [];
    $state_taxonomies_terms = [];
    $location_taxonomies_terms = [];

    // Retrieve data from the terms and termmeta tables for jobs-state first
    foreach ($state_taxonomies as $taxonomy) {
        $state_terms = $wpdb->get_results($wpdb->prepare("SELECT t.term_id, t.name, t.slug, tt.parent FROM {$wpdb->terms} t 
            INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id 
            WHERE tt.taxonomy = %s", $taxonomy));

        $state_taxonomies_terms[] = $state_terms;
    }

    $state_taxonomies_merged = merge_terms($state_taxonomies_terms);

    foreach ($state_taxonomies_merged as $state_term) {
        $new_state_term = wp_insert_term($state_term->name, 'felan_state', array(
            'slug'        => $state_term->slug,
            'parent'      => $state_term->parent,
        ));

        if (!is_wp_error($new_state_term)) {
            $new_state_term_id = $new_state_term['term_id'];
            $old_state_term_id = $state_term->term_id;

            if (is_array($old_state_term_id)) {
                foreach ($old_state_term_id as $key => $value) {
                    // Transfer meta field jobs-location-country to felan-location-country
                    $jobs_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'jobs-state-country'", $value));
                    if ($jobs_state) {
                        update_term_meta($new_state_term_id, 'felan-state-country', $jobs_state);
                    }

                    $company_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'company-state-country'", $value));
                    if ($company_state) {
                        update_term_meta($new_state_term_id, 'felan-state-country', $company_state);
                    }

                    $freelancer_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'freelancer-state-country'", $value));
                    if ($freelancer_state) {
                        update_term_meta($new_state_term_id, 'felan-state-country', $freelancer_state);
                    }

                    $service_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'service-state-country'", $value));
                    if ($service_state) {
                        update_term_meta($new_state_term_id, 'felan-state-country', $service_state);
                    }

                    $project_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'project-state-country'", $value));
                    if ($project_state) {
                        update_term_meta($new_state_term_id, 'felan-state-country', $project_state);
                    }

                    // Save mapping for jobs-location
                    $state_mapping[$value] = $new_state_term_id;
                }
            } else {
                // Transfer meta field jobs-location-country to felan-location-country
                $jobs_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'jobs-state-country'", $old_state_term_id));
                if ($jobs_state) {
                    update_term_meta($new_state_term_id, 'felan-state-country', $jobs_state);
                }

                $company_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'company-state-country'", $old_state_term_id));
                if ($company_state) {
                    update_term_meta($new_state_term_id, 'felan-state-country', $company_state);
                }

                $freelancer_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'freelancer-state-country'", $old_state_term_id));
                if ($freelancer_state) {
                    update_term_meta($new_state_term_id, 'felan-state-country', $freelancer_state);
                }

                $service_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'service-state-country'", $old_state_term_id));
                if ($service_state) {
                    update_term_meta($new_state_term_id, 'felan-state-country', $service_state);
                }

                $project_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'project-state-country'", $old_state_term_id));
                if ($project_state) {
                    update_term_meta($new_state_term_id, 'felan-state-country', $project_state);
                }

                // Save mapping for jobs-location
                $state_mapping[$old_state_term_id] = $new_state_term_id;
            }
        }
    }

    // Retrieve data from the terms and termmeta tables since jobs-location has been removed

    foreach ($location_taxonomies as $taxonomy) {
        $terms = $wpdb->get_results($wpdb->prepare("SELECT t.term_id, t.name, t.slug, tt.parent FROM {$wpdb->terms} t 
            INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id 
            WHERE tt.taxonomy = %s", $taxonomy));

        $location_taxonomies_terms[] = $terms;
    }

    $location_taxonomies_merged = merge_terms($location_taxonomies_terms);

    foreach ($location_taxonomies_merged as $term) {
        // Create new term in felan_location
        $new_term = wp_insert_term($term->name, 'felan_location', array(
            'slug'        => $term->slug,
            'parent'      => $term->parent,
        ));

        if (!is_wp_error($new_term)) {
            $new_term_id = $new_term['term_id'];
            $old_term_id = $term->term_id;

            if (is_array($old_term_id)) {
                foreach ($old_term_id as $key => $value) {
                    // Transfer meta field jobs-location-state to felan-location-state by matching names
                    $jobs_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'jobs-location-state'", $value));

                    if ($jobs_state && isset($state_mapping[$jobs_state])) {
                        update_term_meta($new_term_id, 'felan-location-state', $state_mapping[$jobs_state]);
                    }

                    $company_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'company-location-state'", $value));

                    if ($company_state && isset($state_mapping[$company_state])) {
                        update_term_meta($new_term_id, 'felan-location-state', $state_mapping[$company_state]);
                    }

                    $freelancer_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'freelancer-location-state'", $value));

                    if ($freelancer_state && isset($state_mapping[$freelancer_state])) {
                        update_term_meta($new_term_id, 'felan-location-state', $state_mapping[$freelancer_state]);
                    }

                    $project_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'project-location-state'", $value));

                    if ($project_state && isset($state_mapping[$project_state])) {
                        update_term_meta($new_term_id, 'felan-location-state', $state_mapping[$project_state]);
                    }

                    $service_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'service-location-state'", $value));

                    if ($service_state && isset($state_mapping[$service_state])) {
                        update_term_meta($new_term_id, 'felan-location-state', $state_mapping[$service_state]);
                    }

                    // Save mapping for jobs-location
                    $location_mapping[$value] = $new_term_id;
                }
            } else {
                // Transfer meta field jobs-location-state to felan-location-state by matching names
                $jobs_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'jobs-location-state'", $old_term_id));

                if ($jobs_state && isset($state_mapping[$jobs_state])) {
                    update_term_meta($new_term_id, 'felan-location-state', $state_mapping[$jobs_state]);
                }

                $company_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'company-location-state'", $old_term_id));

                if ($company_state && isset($state_mapping[$company_state])) {
                    update_term_meta($new_term_id, 'felan-location-state', $state_mapping[$company_state]);
                }

                $freelancer_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'freelancer-location-state'", $old_term_id));

                if ($freelancer_state && isset($state_mapping[$freelancer_state])) {
                    update_term_meta($new_term_id, 'felan-location-state', $state_mapping[$freelancer_state]);
                }

                $project_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'project-location-state'", $old_term_id));

                if ($project_state && isset($state_mapping[$project_state])) {
                    update_term_meta($new_term_id, 'felan-location-state', $state_mapping[$project_state]);
                }

                $service_state = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id = %d AND meta_key = 'service-location-state'", $old_term_id));

                if ($service_state && isset($state_mapping[$service_state])) {
                    update_term_meta($new_term_id, 'felan-location-state', $state_mapping[$service_state]);
                }

                // Save mapping for jobs-location
                $location_mapping[$old_term_id] = $new_term_id;
            }
        }
    }

    // Query to get all published 'jobs' posts with taxonomy 'jobs-location' and 'jobs-state'
    $jobs = $wpdb->get_results("
        SELECT p.ID, t.term_id, tt.taxonomy
        FROM {$wpdb->prefix}posts AS p
        LEFT JOIN {$wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id
        LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        LEFT JOIN {$wpdb->prefix}terms AS t ON tt.term_id = t.term_id
        WHERE p.post_type = 'jobs' 
        AND p.post_status = 'publish' 
        AND tt.taxonomy IN ('jobs-location', 'jobs-state')
    ");

    $jobs_data = [];

    if (!empty($jobs)) {
        foreach ($jobs as $job) {
            $post_id = $job->ID;
            $term_id = $job->term_id;
            $taxonomy = $job->taxonomy;

            if (!isset($jobs_data[$post_id])) {
                $jobs_data[$post_id] = [
                    'locations' => [],
                    'states' => [],
                ];
            }

            if ($taxonomy === 'jobs-location') {
                $jobs_data[$post_id]['locations'][] = $term_id;
            } elseif ($taxonomy === 'jobs-state') {
                $jobs_data[$post_id]['states'][] = $term_id;
            }
        }
    }

    // Assign new taxonomy based on mapping
    foreach ($jobs_data as $post_id => $data) {
        if (!empty($data['locations'])) {
            $new_locations = array_map(fn($id) => $location_mapping[$id] ?? null, $data['locations']);
            $new_locations = array_filter($new_locations); // Remove null values
            if (!empty($new_locations)) {
                wp_set_post_terms($post_id, $new_locations, 'felan_location', true);
            }
        }

        if (!empty($data['states'])) {
            $new_states = array_map(fn($id) => $state_mapping[$id] ?? null, $data['states']);
            $new_states = array_filter($new_states); // Remove null values
            if (!empty($new_states)) {
                wp_set_post_terms($post_id, $new_states, 'felan_state', true);
            }
        }
    }

    // Query to get all 'company' posts with 'company-location' and 'company-state' taxonomy
    $company = $wpdb->get_results("
        SELECT p.ID, t.term_id, tt.taxonomy
        FROM {$wpdb->prefix}posts AS p
        LEFT JOIN {$wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id
        LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        LEFT JOIN {$wpdb->prefix}terms AS t ON tt.term_id = t.term_id
        WHERE p.post_type = 'company' 
        AND p.post_status = 'publish' 
        AND tt.taxonomy IN ('company-location', 'company-state')
    ");

    $company_data = [];

    if (!empty($company)) {
        foreach ($company as $item) {
            $post_id = $item->ID;
            $term_id = $item->term_id;
            $taxonomy = $item->taxonomy;

            if (!isset($company_data[$post_id])) {
                $company_data[$post_id] = [
                    'locations' => [],
                    'states' => [],
                ];
            }

            if ($taxonomy === 'company-location') {
                $company_data[$post_id]['locations'][] = $term_id;
            } elseif ($taxonomy === 'company-state') {
                $company_data[$post_id]['states'][] = $term_id;
            }
        }
    }

    // Assign new taxonomy based on mapping
    foreach ($company_data as $post_id => $data) {
        if (!empty($data['locations'])) {
            $new_locations = array_map(fn($id) => $location_mapping[$id] ?? null, $data['locations']);
            $new_locations = array_filter($new_locations); // Remove null values
            if (!empty($new_locations)) {
                wp_set_post_terms($post_id, $new_locations, 'felan_location', true);
            }
        }

        if (!empty($data['states'])) {
            $new_states = array_map(fn($id) => $state_mapping[$id] ?? null, $data['states']);
            $new_states = array_filter($new_states); // Remove null values
            if (!empty($new_states)) {
                wp_set_post_terms($post_id, $new_states, 'felan_state', true);
            }
        }
    }

    // Query to get all 'freelancer' posts with 'freelancer_locations' and 'freelancer-state' taxonomy
    $freelancer = $wpdb->get_results("
        SELECT p.ID, t.term_id, tt.taxonomy
        FROM {$wpdb->prefix}posts AS p
        LEFT JOIN {$wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id
        LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        LEFT JOIN {$wpdb->prefix}terms AS t ON tt.term_id = t.term_id
        WHERE p.post_type = 'freelancer' 
        AND p.post_status = 'publish' 
        AND tt.taxonomy IN ('freelancer_locations', 'freelancer-state')
    ");

    $freelancer_data = [];

    if (!empty($freelancer)) {
        foreach ($freelancer as $item) {
            $post_id = $item->ID;
            $term_id = $item->term_id;
            $taxonomy = $item->taxonomy;

            if (!isset($freelancer_data[$post_id])) {
                $freelancer_data[$post_id] = [
                    'locations' => [],
                    'states' => [],
                ];
            }

            if ($taxonomy === 'freelancer_locations') {
                $freelancer_data[$post_id]['locations'][] = $term_id;
            } elseif ($taxonomy === 'freelancer-state') {
                $freelancer_data[$post_id]['states'][] = $term_id;
            }
        }
    }

    // Assign new taxonomy based on mapping
    foreach ($freelancer_data as $post_id => $data) {
        if (!empty($data['locations'])) {
            $new_locations = array_map(fn($id) => $location_mapping[$id] ?? null, $data['locations']);
            $new_locations = array_filter($new_locations); // Remove null values
            if (!empty($new_locations)) {
                wp_set_post_terms($post_id, $new_locations, 'felan_location', true);
            }
        }

        if (!empty($data['states'])) {
            $new_states = array_map(fn($id) => $state_mapping[$id] ?? null, $data['states']);
            $new_states = array_filter($new_states); // Remove null values
            if (!empty($new_states)) {
                wp_set_post_terms($post_id, $new_states, 'felan_state', true);
            }
        }
    }

    // Query to get all 'service' posts with 'service-location' and 'service-state' taxonomy
    $service = $wpdb->get_results("
        SELECT p.ID, t.term_id, tt.taxonomy
        FROM {$wpdb->prefix}posts AS p
        LEFT JOIN {$wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id
        LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        LEFT JOIN {$wpdb->prefix}terms AS t ON tt.term_id = t.term_id
        WHERE p.post_type = 'service' 
        AND p.post_status = 'publish' 
        AND tt.taxonomy IN ('service-location', 'service-state')
    ");

    $service_data = [];

    if (!empty($service)) {
        foreach ($service as $item) {
            $post_id = $item->ID;
            $term_id = $item->term_id;
            $taxonomy = $item->taxonomy;

            if (!isset($service_data[$post_id])) {
                $service_data[$post_id] = [
                    'locations' => [],
                    'states' => [],
                ];
            }

            if ($taxonomy === 'service-location') {
                $service_data[$post_id]['locations'][] = $term_id;
            } elseif ($taxonomy === 'service-state') {
                $service_data[$post_id]['states'][] = $term_id;
            }
        }
    }

    // Assign new taxonomy based on mapping
    foreach ($service_data as $post_id => $data) {
        if (!empty($data['locations'])) {
            $new_locations = array_map(fn($id) => $location_mapping[$id] ?? null, $data['locations']);
            $new_locations = array_filter($new_locations); // Remove null values
            if (!empty($new_locations)) {
                wp_set_post_terms($post_id, $new_locations, 'felan_location', true);
            }
        }

        if (!empty($data['states'])) {
            $new_states = array_map(fn($id) => $state_mapping[$id] ?? null, $data['states']);
            $new_states = array_filter($new_states); // Remove null values
            if (!empty($new_states)) {
                wp_set_post_terms($post_id, $new_states, 'felan_state', true);
            }
        }
    }

    // Query to get all 'project' posts with 'project-location' and 'project-state' taxonomy
    $project = $wpdb->get_results("
        SELECT p.ID, t.term_id, tt.taxonomy
        FROM {$wpdb->prefix}posts AS p
        LEFT JOIN {$wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id
        LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        LEFT JOIN {$wpdb->prefix}terms AS t ON tt.term_id = t.term_id
        WHERE p.post_type = 'project' 
        AND p.post_status = 'publish' 
        AND tt.taxonomy IN ('project-location', 'project-state')
    ");

    $project_data = [];

    if (!empty($project)) {
        foreach ($project as $item) {
            $post_id = $item->ID;
            $term_id = $item->term_id;
            $taxonomy = $item->taxonomy;

            if (!isset($project_data[$post_id])) {
                $project_data[$post_id] = [
                    'locations' => [],
                    'states' => [],
                ];
            }

            if ($taxonomy === 'project-location') {
                $project_data[$post_id]['locations'][] = $term_id;
            } elseif ($taxonomy === 'project-state') {
                $project_data[$post_id]['states'][] = $term_id;
            }
        }
    }

    // Assign new taxonomy based on mapping
    foreach ($project_data as $post_id => $data) {
        if (!empty($data['locations'])) {
            $new_locations = array_map(fn($id) => $location_mapping[$id] ?? null, $data['locations']);
            $new_locations = array_filter($new_locations); // Remove null values
            if (!empty($new_locations)) {
                wp_set_post_terms($post_id, $new_locations, 'felan_location', true);
            }
        }

        if (!empty($data['states'])) {
            $new_states = array_map(fn($id) => $state_mapping[$id] ?? null, $data['states']);
            $new_states = array_filter($new_states); // Remove null values
            if (!empty($new_states)) {
                wp_set_post_terms($post_id, $new_states, 'felan_state', true);
            }
        }
    }
}

// Hook into admin_init to run when accessing the admin page
add_action('admin_init', 'migrate_taxonomy_data');

function merge_terms($arrays)
{
    $merged = [];

    foreach ($arrays as $array) {
        foreach ($array as $term) {
            $key = $term->slug; // Use slug as key to avoid duplicate names
            if (!isset($merged[$key])) {
                $merged[$key] = (object) [
                    'term_id' => [$term->term_id],
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'parent' => $term->parent
                ];
            } else {
                $merged[$key]->term_id[] = $term->term_id;
            }
        }
    }

    return array_values($merged);
}
