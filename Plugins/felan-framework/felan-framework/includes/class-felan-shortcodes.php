<?php

if (!defined("ABSPATH")) {
    exit();
}
if (!class_exists("Felan_Shortcode_Jobs")) {
    /**
     * Class Felan_Shortcode_Jobs
     */
    class Felan_Shortcode_Jobs
    {
        /**
         * Constructor.
         */
        public function __construct()
        {
            //Employer
            add_shortcode("felan_dashboard", [$this, "dashboard_employer"]);
            add_shortcode("felan_jobs", [$this, "jobs"]);
            add_shortcode("felan_jobs_performance", [$this, "jobs_performance"]);
            add_shortcode("felan_jobs_submit", [$this, "jobs_submit"]);
            add_shortcode("felan_freelancers", [$this, "freelancers"]);
            add_shortcode("felan_user_package", [$this, "user_package"]);
            add_shortcode("felan_messages", [$this, "messages"]);
            add_shortcode("felan_company", [$this, "company"]);
            add_shortcode("felan_submit_company", [$this, "submit_company"]);
            add_shortcode("felan_settings", [$this, "employer_settings"]);
            add_shortcode("felan_meetings", [$this, "employer_meetings"]);
            add_shortcode("felan_package", [$this, "package"]);
            add_shortcode("felan_payment", [$this, "payment"]);
            add_shortcode("felan_wallet", [$this, "employer_wallet"]);
            add_shortcode("felan_payment_completed", [
                $this,
                "payment_completed",
            ]);

            //Freelancer
            add_shortcode("felan_freelancer_dashboard", [
                $this,
                "dashboard_freelancer",
            ]);
            add_shortcode("felan_freelancer_settings", [
                $this,
                "freelancer_settings",
            ]);
            add_shortcode("felan_my_jobs", [$this, "my_jobs"]);
            add_shortcode("felan_freelancer_company", [
                $this,
                "freelancer_company",
            ]);
            add_shortcode("felan_freelancer_profile", [
                $this,
                "freelancer_profile",
            ]);
			add_shortcode("felan_user_verification", [
                $this,
                "user_verify",
            ]);
            add_shortcode("felan_freelancer_my_review", [
                $this,
                "freelancer_my_review",
            ]);
            add_shortcode("felan_freelancer_meetings", [
                $this,
                "freelancer_meetings",
            ]);

            add_shortcode("felan_freelancer_user_package", [$this, "freelancer_user_package"]);
            add_shortcode("felan_freelancer_package", [$this, "freelancer_package"]);
            add_shortcode("felan_freelancer_payment", [$this, "freelancer_payment"]);
            add_shortcode("felan_freelancer_payment_completed", [$this, "freelancer_payment_completed"]);
            add_shortcode("felan_freelancer_wallet", [$this, "freelancer_wallet"]);

            //Service
            add_shortcode("felan_submit_service", [$this, "submit_service"]);
            add_shortcode("felan_service_payment", [$this, "service_payment"]);
            add_shortcode("felan_service_payment_completed", [$this, "service_payment_completed"]);
            add_shortcode("felan_employer_service", [$this, "employer_service"]);
            add_shortcode("felan_freelancer_service", [$this, "my_service"]);

            //Project
            add_shortcode("felan_projects", [$this, "projects"]);
            add_shortcode("felan_projects_submit", [$this, "projects_submit"]);
            add_shortcode("felan_my_project", [$this, "my_project"]);
            add_shortcode("felan_project_payment", [$this, "project_payment"]);
            add_shortcode("felan_project_payment_completed", [$this, "project_payment_completed"]);

            add_shortcode("felan_proposals", [$this, "proposals"]);


            //Disputes
            add_shortcode("felan_disputes", [$this, "employer_disputes"]);
            add_shortcode("felan_freelancer_disputes", [$this, "freelancer_disputes",]);

            //Thank You
            add_shortcode("felan_thank_you", [$this, "thank_you"]);
        }

        /**
         * Thank You
         */
        public function thank_you()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles) || in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("dashboard/wallet-topup/thank-you.php");
            } else {
                ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                        "Please access the role Employer/Freelancer to view",
                        "felan-framework"
                    ); ?></p>
                <?php
            }
            return ob_get_clean();
        }

        /**
         * Dashboard Employer
         */
        public function dashboard_employer()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("dashboard/employer/dashboard.php");
            } else {
?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?></p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Dashboard Freelancer
         */
        public function dashboard_freelancer()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("dashboard/freelancer/dashboard.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Freelancer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Projects
         */
        public function projects()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("dashboard/employer/projects.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

		/**
         * Proposals
         */
        public function proposals()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("dashboard/employer/proposals.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Projects Submit
         */
        public function projects_submit()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("project/submit.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * My Project
         */
        public function my_project()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("dashboard/freelancer/my-project.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Freelancer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Project Payment
         */
        public function project_payment()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("dashboard/employer/project/payment.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Project Payment Completed
         */
        public function project_payment_completed()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("dashboard/employer/project/payment-completed.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Jobs
         */
        public function jobs()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("dashboard/employer/jobs.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Jobs Performance
         */
        public function jobs_performance()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("dashboard/employer/jobs-performance.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Jobs Submit
         */
        public function jobs_submit()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("jobs/submit.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Freelancers
         */
        public function freelancers()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("dashboard/employer/freelancers.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * User Package
         */
        public function user_package()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("dashboard/employer/user-package.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Messages
         */
        public function messages()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles) || in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("dashboard/messages/messages.php");
            } else { ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer,Freelancer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }

            return ob_get_clean();
        }

        /**
         * Meetings
         */
        public function employer_meetings()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("dashboard/employer/meetings.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Wallet
         */
        public function employer_wallet()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("dashboard/employer/wallet.php");
            } else {
                ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                        "Please access the role Employer to view",
                        "felan-framework"
                    ); ?>
                </p>
                <?php
            }
            return ob_get_clean();
        }

        /**
         * Employer Disputes
         */
        public function employer_disputes()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("dashboard/employer/disputes.php");
            } else {
                ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                        "Please access the role Employer to view",
                        "felan-framework"
                    ); ?>
                </p>
                <?php
            }
            return ob_get_clean();
        }

        /**
         * Company
         */
        public function company()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("dashboard/employer/company.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Submit Company
         */
        public function submit_company()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("company/submit.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Settings employer
         */
        public function employer_settings()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("dashboard/employer/settings.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Service Payment
         */
        public function service_payment()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("dashboard/employer/service/payment.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Employer Service
         */
        public function employer_service()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("dashboard/employer/service.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Settings freelancer
         */
        public function freelancer_settings()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("dashboard/freelancer/settings.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Freelancer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Package
         */
        public function package()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("jobs/package/package.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Freelancer or Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Payment
         */
        public function payment()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("jobs/payment/payment.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Payment_completed
         */
        public function payment_completed()
        {
            ob_start();
            global $current_user;
            if (
                in_array("felan_user_freelancer", (array)$current_user->roles) ||
                in_array("felan_user_employer", (array)$current_user->roles)
            ) {
                felan_get_template("jobs/payment/payment-completed.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * My Jobs
         */
        public function my_jobs()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("dashboard/freelancer/my-jobs.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Freelancer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Freelancer Profile
         */
        public function freelancer_company()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("dashboard/freelancer/company.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Freelancer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Freelancer Profile
         */
        public function freelancer_profile()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("dashboard/freelancer/profile.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Freelancer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

		/**
         * User Identity Verification
         */
        public function user_verify()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_freelancer", (array)$current_user->roles) ||
				in_array("felan_user_employer", (array)$current_user->roles)
			) {
                felan_get_template("dashboard/freelancer/verify.php");
            } else {
            ?>
                <p class="notice">
					<i class="far fa-exclamation-circle"></i>
					<?php esc_html_e("Please access the role Freelancer or Employer to view", "felan-framework"); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Freelancer Reviews
         */
        public function freelancer_my_review()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("dashboard/freelancer/my-review.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Freelancer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Freelancer Meetings
         */
        public function freelancer_meetings()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("dashboard/freelancer/meetings.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Freelancer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Freelancer disputes
         */
        public function freelancer_disputes()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("dashboard/freelancer/disputes.php");
            } else {
                ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                        "Please access the role Freelancer to view",
                        "felan-framework"
                    ); ?>
                </p>
                <?php
            }
            return ob_get_clean();
        }

        /**
         * Service Package
         */
        public function freelancer_package()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("freelancer/package/package.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Freelancer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Freelancer Wallet
         */
        public function freelancer_wallet()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("freelancer/wallet.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Freelancer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Service Payment
         */
        public function freelancer_payment()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("freelancer/payment/payment.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Freelancer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }


        /**
         * Freelancer Payment Completed
         */
        public function freelancer_payment_completed()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("freelancer/payment/payment-completed.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Freelancer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Freelancer Reviews
         */
        public function freelancer_user_package()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("dashboard/freelancer/user-package.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Freelancer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Submit Service
         */
        public function submit_service()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("service/submit.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Freelancer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * My Service
         */
        public function my_service()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                felan_get_template("dashboard/freelancer/service.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Freelancer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Service Payment Completed
         */
        public function service_payment_completed()
        {
            ob_start();
            global $current_user;
            if (in_array("felan_user_employer", (array)$current_user->roles)) {
                felan_get_template("dashboard/employer/service/payment-completed.php");
            } else {
            ?>
                <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "felan-framework"
                                                                            ); ?>
                </p>
<?php
            }
            return ob_get_clean();
        }
    }

    new Felan_Shortcode_Jobs();
}
