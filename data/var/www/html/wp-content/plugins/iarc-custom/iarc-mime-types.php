<?php 

// IARC L.Alteyrac 20151007: add some extensions to allow files to be uploaded in the ELN
// @see http://ask.xmodulo.com/this-file-type-is-not-permitted-for-security-reasons.html
function enable_extended_upload ( $mime_types =array() ) {
 
   // The MIME types listed here will be allowed in the media library.
   $mime_types['sh']  = 'text/script';
   $mime_types['pl']  = 'text/script';
   $mime_types['r']  = 'text/script';
    
   // If you want to forbid specific file types which are otherwise allowed,
   // specify them here.  You can add as many as possible.
   //unset( $mime_types['exe'] );
 
   return $mime_types;
}
add_filter('upload_mimes', 'enable_extended_upload');

?>