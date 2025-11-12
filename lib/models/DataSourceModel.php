<?php

if (!defined('ABSPATH'))
    exit;

if (!class_exists('Oc3Sengine_DataSourceModel')) {

    class Oc3Sengine_DataSourceModel {

        public function searchPosts($post_type = '', $srch = '', $page = 1, $records_per_page = 20) {
            global $wpdb;
            $par_arr = [0, 100];

            $search_present = false;
            //$indexed is passed
            $limit_part_present = false;
            if (strlen($srch) > 0) {
                $search_present = true;
                $search = sanitize_text_field($srch);
            }
            if ($records_per_page > 0 && $page > 0) {
                $limit_part_present = true;
                $par_arr[0] = ($page - 1) * $records_per_page;
                $par_arr[1] = $records_per_page;
            }

            if ($search_present && $limit_part_present) {
                if (strlen($post_type) > 0) {

                    $cnt = $wpdb->get_var(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery */
                                $wpdb->prepare("SELECT COUNT(*) FROM "
                                    . $wpdb->prefix . "posts as a "
                                    . " WHERE  (post_status = 'draft' OR post_status = 'publish') AND"
                                    . "  (post_content LIKE %s OR post_title LIKE %s ) "
                                    . " AND post_type = %s  "
                                    , ['%' . $search . '%', '%' . $search . '%', sanitize_text_field($post_type)]));

                    $rows = $wpdb->get_results(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
                                $wpdb->prepare("SELECT * FROM " .
                                    $wpdb->prefix . "posts  as a "
                                    . " WHERE  (post_status = 'draft' OR post_status = 'publish') AND"
                                    . "  (post_content LIKE %s OR post_title LIKE %s ) "
                                    . " AND post_type = %s  "
                                    . "   ORDER BY a.ID   LIMIT  %d,%d  ", ['%' . $search . '%',
                                '%' . $search . '%', sanitize_text_field($post_type),
                                $par_arr[0], $par_arr[1]]));
                } else {
                    $type_str = " AND ( post_type = 'post' OR   post_type = 'page') ";

                    $cnt = $wpdb->get_var(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
                                $wpdb->prepare("SELECT COUNT(*) FROM "
                                    . $wpdb->prefix . "posts as a "
                                    . " WHERE  (post_status = 'draft' OR post_status = 'publish') AND"
                                    . "  (post_content LIKE %s OR post_title LIKE %s ) "
                                    . " AND ( post_type = 'post' OR   post_type = 'page') "
                                    , ['%' . $search . '%', '%' . $search . '%']));

                    $rows = $wpdb->get_results(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
                                $wpdb->prepare("SELECT * FROM " .
                                    $wpdb->prefix . "posts  as a "
                                    . " WHERE  (post_status = 'draft' OR post_status = 'publish') AND"
                                    . "  (post_content LIKE %s OR post_title LIKE %s ) "
                                    . " AND ( post_type = 'post' OR   post_type = 'page') "
                                    . "   ORDER BY a.ID   LIMIT  %d,%d  ", ['%' . $search . '%',
                                '%' . $search . '%',
                                $par_arr[0], $par_arr[1]]));
                }
            } elseif ($limit_part_present) {
                if (strlen($post_type) > 0) {

                    $cnt = $wpdb->get_var(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
                                $wpdb->prepare("SELECT COUNT(*) FROM "
                                    . $wpdb->prefix . "posts as a "
                                    . " WHERE  (post_status = 'draft' OR post_status = 'publish') "
                                    . " AND post_type = %s  "
                                    , [sanitize_text_field($post_type)]));

                    $rows = $wpdb->get_results(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
                                $wpdb->prepare("SELECT * FROM " .
                                    $wpdb->prefix . "posts  as a "
                                    . " WHERE  (post_status = 'draft' OR post_status = 'publish') "
                                    . " AND post_type = %s  "
                                    . "   ORDER BY a.ID   LIMIT  %d,%d  ", [
                                sanitize_text_field($post_type),
                                $par_arr[0], $par_arr[1]]));
                } else {

                    $cnt = $wpdb->get_var(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
                                $wpdb->prepare("SELECT COUNT(*) FROM "
                                    . $wpdb->prefix . "posts as a "
                                    . " WHERE %d AND (post_status = 'draft' OR post_status = 'publish') "
                                    . " AND ( post_type = 'post' OR   post_type = 'page') "
                                    , [1]));

                    $rows = $wpdb->get_results(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
                                $wpdb->prepare("SELECT * FROM " .
                                    $wpdb->prefix . "posts  as a "
                                    . " WHERE  (post_status = 'draft' OR post_status = 'publish') "
                                    . " AND ( post_type = 'post' OR   post_type = 'page') "
                                    . "   ORDER BY a.ID   LIMIT  %d,%d  ", [
                                $par_arr[0], $par_arr[1]]));
                }
            } else {
                if (strlen($post_type) > 0) {

                    $cnt = $wpdb->get_var(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
                                $wpdb->prepare("SELECT COUNT(*) FROM "
                                    . $wpdb->prefix . "posts as a "
                                    . " WHERE  (post_status = 'draft' OR post_status = 'publish') "
                                    . " AND post_type = %s  "
                                    , [sanitize_text_field($post_type)]));

                    $rows = $wpdb->get_results(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
                                $wpdb->prepare("SELECT * FROM " .
                                    $wpdb->prefix . "posts  as a "
                                    . " WHERE  (post_status = 'draft' OR post_status = 'publish') "
                                    . " AND post_type = %s  "
                                    . "   ORDER BY a.ID   ", [
                                sanitize_text_field($post_type)]));
                } else {

                    $cnt = $wpdb->get_var(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
                            $wpdb->prepare("SELECT COUNT(*) FROM "
                                    . $wpdb->prefix . "posts as a "
                                    . " WHERE %d AND (post_status = 'draft' OR post_status = 'publish') "
                                    . " AND ( post_type = 'post' OR   post_type = 'page') "
                                    , [1]));

                    $rows = $wpdb->get_results(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
                            $wpdb->prepare("SELECT * FROM " .
                                    $wpdb->prefix . "posts  as a "
                                    . " WHERE %d AND  (post_status = 'draft' OR post_status = 'publish') AND"
                                    . " AND ( post_type = 'post' OR   post_type = 'page') "
                                    . "   ORDER BY a.ID   ", [
                                1]));
                }
            }



            $new_rows = [];
            foreach ($rows as $rv) {

                $row = new stdClass();
                $row->ID = (int) $rv->ID;
                $row->id = (int) $rv->ID;
                $row->post_author = (int) $rv->post_author;
                $row->post_content = sanitize_textarea_field($rv->post_content);
                $row->post_title = sanitize_text_field($rv->post_title);
                $row->post_status = sanitize_text_field($rv->post_status);
                $row->post_type = sanitize_text_field($rv->post_type);
                $row->post_editurl = get_edit_post_link($rv->ID, 'edit');
                $new_rows[(int) $rv->ID] = $row;
            }
            return ['cnt' => $cnt, 'rows' => $new_rows];
        }
    }

}
