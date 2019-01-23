<?php

/**
 * Contact Form Class
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    Realtypress
 * @subpackage Realtypress/admin
 **/
class Realtypress_Contact {

    function __construct()
    {
        $this->favorites_meta_key   = 'rps_favorite';
        $this->favorites_cookie_key = 'rps-favorite-posts';

        $this->tpl = new RealtyPress_Template();
    }

    function send_listing_contact_email( $input )
    {

        // sanitize form values
        $name      = sanitize_text_field( $input["cf-name"] );
        $email     = sanitize_email( $input["cf-email"] );
        $subject   = sanitize_text_field( $input["cf-subject"] );
        $message   = sanitize_text_field( $input["cf-message"] );
        $permalink = esc_url( $input["permalink"] );

        do_action( 'realtypress_before_listing_contact_email_send', $name, $email, $subject, $message, $permalink );

        $active_theme = get_option( 'rps-general-theme', 'default' );
        $template_dir = REALTYPRESS_TEMPLATE_URL . '/' . $active_theme;

        $body = $this->tpl->get_template_part( '/emails/email-listing-contact' );
        $body = str_replace( '{%DATE%}', date( "F d Y H:i:s" ), $body );
        $body = str_replace( '{%NAME%}', $name, $body );
        $body = str_replace( '{%EMAIL%}', $email, $body );
        $body = str_replace( '{%MESSAGE%}', nl2br( $message ), $body );
        $body = str_replace( '{%LINK%}', $permalink, $body );

        $send = array(
            'name'    => $name,
            'email'   => $email,
            'subject' => $subject,
            'body'    => $body,
        );

        $sent = false;

        // Send to agent address
        // =====================

        if( ! empty( $input["agents"] ) ) {

            $to_agent_ids    = get_option( 'rps-general-agent-contact-id', 'default' );
            $to_agent_emails = get_option( 'rps-general-agent-contact-email', 'default' );
            $agents          = explode( ',', $input["agents"] );

            foreach( $agents as $agent ) {
                if( in_array( $agent, $to_agent_ids ) ) {

                    // Custom agent email found. Send inquiry to agent email.
                    $key        = array_search( $agent, $to_agent_ids );
                    $send['to'] = $to_agent_emails[$key];
                    $mail       = $this->deliver_mail( $send );
                    $sent       = true;
                }
            }
        }

        // Send to default email address
        // =============================

        if( $sent == false ) {
            $mail = $this->deliver_mail( $send );
        }

        return $mail;
    }

    function send_contact_email( $input )
    {

        // sanitize form values
        $name    = sanitize_text_field( $input["cf-name"] );
        $email   = sanitize_email( $input["cf-email"] );
        $subject = sanitize_text_field( $input["cf-subject"] );
        $message = sanitize_text_field( $input["cf-message"] );

        $active_theme = get_option( 'rps-general-theme', 'default' );
        $template_dir = REALTYPRESS_TEMPLATE_URL . '/' . $active_theme;

        $body = $this->tpl->get_template_part( '/emails/email-generic-contact' );
        $body = str_replace( '{%DATE%}', date( "F d Y H:i:s" ), $body );
        $body = str_replace( '{%NAME%}', $name, $body );
        $body = str_replace( '{%EMAIL%}', $email, $body );
        $body = str_replace( '{%SUBJECT%}', $subject, $body );
        $body = str_replace( '{%MESSAGE%}', nl2br( $message ), $body );

        $send = array(
            'name'    => $name,
            'email'   => $email,
            'subject' => $subject,
            'body'    => $body,
        );

        $mail = $this->deliver_mail( $send );

        return $mail;
    }

    /**
     * Deliver mail
     * @param  array $send ['subject'] Subject of the email
     *                         ['body'] Body of the email
     *                         ['name'] Send name
     *                         ['email'] Send email
     * @return array  $return  ['output'] Result message
     *                         ['errors'] Result true/false
     */
    function deliver_mail( $send )
    {

        // All mail is delivered to user set in RealtyPress general contact email field, or will be sent too admin address as fallback.

        if( ! empty( $send['to'] ) ) {
            $to = $send['to'];
        }
        else {
            $admin_email = get_option( 'admin_email' );
            $to          = get_option( 'rps-general-contact-email', $admin_email );
        }

        $disable_from_header = get_option( 'rps-system-options-disable-from-headers', 1 );

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=ISO-8859-1\r\n";

        if( $disable_from_header == false ) {
            $headers .= "From: " . $send['name'] . " <" . strip_tags( $send['email'] ) . ">\r\n";
            $headers .= "Reply-To: " . strip_tags( $send['email'] ) . "\r\n";
        }

        $return = array();

        $wp_email = wp_mail( $to, $send['subject'], $send['body'], $headers );

        if( $wp_email ) {
            $return['output'] = __( 'Thank you for contacting us!<br>We\'ll be in touch asap.', 'realtypress-premium' );
            $return['errors'] = false;
        }
        else {
            $return['output'] = __( 'An unexpected error occurred.', 'realtypress-premium' );
            $return['errors'] = true;
        }

        return $return;
    }

    /**
     * --------------------------------------------------------------
     *   Math Captcha
     * --------------------------------------------------------------
     */

    // Generate math problem for unknown users
    function get_math_problem()
    {

        // only if this function was called exactly once
        static $problem_fired = 0;

        if( $problem_fired ++ > 0 )
            return false;

        // Support cross domain AJAX call
        header( 'Access-Control-Allow-Origin: ' . home_url() );

        session_name( 'math-captcha' );
        session_start();

        list( $problem, $answer, $uniqueid ) = $this->rand_engine();

        //Store them into session data
        $_SESSION[$uniqueid]['answer'] = $answer;

        //Filter specific string
        /** @noinspection SpellCheckingInspection */
        $stringToBeReplace = array(
            '%problem%',
            '%uniqueid%',
            '%sessionid%',
            '%problemlabel%',
            '%reloadbutton%'
        );
        $stringToReplace   = array(
            $problem,
            $uniqueid,
            session_id(),
            __( 'What is ', 'realtypress-premium' ),
            __( 'Change Question', 'realtypress-premium' )
        );
        $fireworks         = str_replace( $stringToBeReplace, $stringToReplace, $this->get_quiz_form() );

        return $fireworks;
        // }

    }

    // Match Captcha Form
    function get_quiz_form()
    {

        $output = '<div id="math-quiz">';
        $output .= '<label>';
        $output .= '%problemlabel%';
        $output .= '%problem%';
        $output .= '</label>';
        $output .= '<input type="text" name="math-quiz" class="form-control" placeholder="' . __( 'Answer', 'realtypress-premium' ) . '" />';
        $output .= '<input type="hidden" name="unique_id" value="%uniqueid%" />';
        $output .= '<input type="hidden" name="session_id" value="%sessionid%" />';
        $output .= ' <small><a href="#" class="refresh-math-captcha">%reloadbutton% <i class="fa fa-refresh"></i></a></small>';
        $output .= '</div>';

        return $output;
    }

    // Random number generator
    function rand_engine()
    {

        $operators = array( '+', '-' );
        $operator  = $operators[array_rand( $operators )];

        $numbers    = array();
        $numbers[0] = mt_rand( 15, 25 );
        $numbers[1] = mt_rand( 4, 14 );

        $answer  = ( $operator == '+' ) ? $numbers[0] + $numbers[1] : $numbers[0] - $numbers[1];
        $problem = $numbers[0] . ' ' . $operator . ' ' . $numbers[1] . ' ?';

        //Random string generator
        $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
        $unique_id  = '';
        for( $p = 0; $p < 32; $p ++ ) {
            $unique_id .= $characters[mt_rand( 0, strlen( $characters ) - 1 )];
        }

        return array( $problem, $answer, $unique_id );
    }

} // end of class