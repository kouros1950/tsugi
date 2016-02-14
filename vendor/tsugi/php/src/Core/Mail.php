<?php

namespace Tsugi\Core;

/** Mail utilities */

class Mail {

    public static function computeCheck($identity) {
        global $CFG;
        return sha1($CFG->mailsecret . '::' . $identity);
    }

    public static function send($to, $subject, $message, $id, $token) {
        global $CFG;

        if ( (!isset($CFG->maildomain)) || $CFG->maildomain === false ) return;

        if ( isset($CFG->maileol) && isset($CFG->wwwroot) && isset($CFG->maildomain) ) {
            // All good
        } else {
            die_with_error_log("Incomplete mail configuration in mailSend");
        }

        if ( strlen($to) < 1 || strlen($subject) < 1 || strlen($id) < 1 || strlen($token) < 1 ) return false;

        $EOL = $CFG->maileol;
        $maildomain = $CFG->maildomain;
        $manage = $CFG->wwwroot . "/profile.php";
        $unsubscribe_url = $CFG->wwwroot . "/unsubscribe.php?id=$id&token=$token";
        $msg = $message;
        if ( substr($msg,-1) != "\n" ) $msg .= "\n";
        // $msg .= "\nYou can manage your mail preferences at $manage \n";
        // TODO: Make unsubscribe work

        // echo $msg;

        $headers = "From: no-reply@$maildomain" . $EOL .
            "Return-Path: <bounced-$id-$token@$maildomain>" . $EOL .
            "List-Unsubscribe: <$unsubscribe_url>" . $EOL .
            'X-Mailer: PHP/' . phpversion();

        error_log("Mail to: $to $subject");
        // echo $headers;
        return mail($to,$subject,$msg,$headers);
    }
}