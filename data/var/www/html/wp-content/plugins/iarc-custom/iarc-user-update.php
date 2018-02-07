<?php

/*
 * IARC L.Alteyrac 20160822
 * Custom Email message when a user changes his/her password
 * @see wp-includes/user.php for original message
*/


function iarc_change_password_mail_message( $pass_change_mail, $user, $userdata ) {

  $new_message_txt = __( 'Dear ' . $user['first_name'] . ',

This notice confirms that your password was changed on ###SITEURL###

If you did not change your password, please contact the ELN Team at eln@iarc.fr

Have a nice day,

Lucile' );

  $pass_change_mail[ 'message' ] = $new_message_txt;
  return $pass_change_mail;
}
add_filter( 'password_change_email', 'iarc_change_password_mail_message', 10, 3 );

?>